<?php

namespace App\Http\Controllers;

use App\Models\Importacion;
use App\Models\Producto;
use App\Models\Proveedore;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportacionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-importacion|crear-importacion', ['only' => ['index']]);
        $this->middleware('permission:crear-importacion', ['only' => ['create', 'store']]);
        $this->middleware('permission:ver-importacion', ['only' => ['show']]);
    }

    public function index(): View
    {
        $importaciones = Importacion::with(['proveedor.persona', 'user'])
            ->latest()
            ->paginate(20);
        return view('importacion.index', compact('importaciones'));
    }

    public function create(): View
    {
        $proveedores = Proveedore::with('persona')->get();
        $productos = Producto::where('estado', 1)->where('origen', 'Importado')->get();
        return view('importacion.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pais_origen'    => 'required|string|max:100',
            'fecha_llegada'  => 'required|date',
            'moneda_costo'   => 'required|in:COP,USD,EUR,CNY',
            'tasa_cambio'    => 'required|numeric|min:1',
            'flete'          => 'nullable|numeric|min:0',
            'seguro'         => 'nullable|numeric|min:0',
            'arancel'        => 'nullable|numeric|min:0',
            'otros_gastos'   => 'nullable|numeric|min:0',
            'notas'          => 'nullable|string',
            'productos'      => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad'    => 'required|integer|min:1',
            'productos.*.costo_unitario_moneda' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $importacion = Importacion::create([
                'proveedor_id'  => $request->proveedor_id,
                'user_id'       => Auth::id(),
                'pais_origen'   => $request->pais_origen,
                'fecha_llegada' => $request->fecha_llegada,
                'moneda_costo'  => $request->moneda_costo,
                'tasa_cambio'   => $request->tasa_cambio,
                'flete'         => $request->flete ?? 0,
                'seguro'        => $request->seguro ?? 0,
                'arancel'       => $request->arancel ?? 0,
                'otros_gastos'  => $request->otros_gastos ?? 0,
                'notas'         => $request->notas,
            ]);

            $totalUnidades = collect($request->productos)->sum('cantidad');
            $gastosTotal = $importacion->total_gastos;
            $gastosPorUnidad = $totalUnidades > 0 ? $gastosTotal / $totalUnidades : 0;

            foreach ($request->productos as $item) {
                $costoEnCop = $item['costo_unitario_moneda'] * $request->tasa_cambio;
                $costoFinal = $costoEnCop + $gastosPorUnidad;

                $importacion->productos()->attach($item['producto_id'], [
                    'id'                     => \Illuminate\Support\Str::uuid()->toString(),
                    'variante_id'            => $item['variante_id'] ?? null,
                    'cantidad'               => $item['cantidad'],
                    'costo_unitario_moneda'  => $item['costo_unitario_moneda'],
                    'costo_unitario_cop'     => $costoFinal,
                ]);
            }

            ActivityLogService::log('Registro de importación', 'Importaciones', ['numero' => $importacion->numero]);
            DB::commit();
            return redirect()->route('importaciones.index')
                ->with('success', 'Importación registrada: ' . $importacion->numero);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear importación', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Importacion $importacion): View
    {
        $importacion->load(['productos', 'proveedor.persona', 'user']);
        return view('importacion.show', compact('importacion'));
    }
}
