<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DevolucionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-devolucion|crear-devolucion', ['only' => ['index']]);
        $this->middleware('permission:crear-devolucion', ['only' => ['create', 'store']]);
        $this->middleware('permission:ver-devolucion', ['only' => ['show']]);
        $this->middleware('permission:editar-devolucion', ['only' => ['aprobar', 'rechazar']]);
    }

    public function index(): View
    {
        $devoluciones = Devolucion::with(['venta', 'user'])
            ->latest()
            ->paginate(20);
        return view('devolucion.index', compact('devoluciones'));
    }

    public function create(Request $request): View
    {
        $venta = null;
        if ($request->venta_id) {
            $venta = Venta::with(['productos.inventario', 'cliente.persona'])->find($request->venta_id);
        }
        $ventas = Venta::with('cliente.persona')->latest()->limit(50)->get();
        return view('devolucion.create', compact('venta', 'ventas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'venta_id'   => 'required|exists:ventas,id',
            'tipo'       => 'required|in:Devolucion,Cambio',
            'motivo'     => 'required|string|max:500',
            'notas'      => 'nullable|string',
            'productos'  => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad'    => 'required|integer|min:1',
            'productos.*.precio_venta' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = collect($request->productos)->sum(fn($p) => $p['cantidad'] * $p['precio_venta']);

            $devolucion = Devolucion::create([
                'venta_id' => $request->venta_id,
                'tipo'     => $request->tipo,
                'motivo'   => $request->motivo,
                'notas'    => $request->notas,
                'total'    => $total,
            ]);

            foreach ($request->productos as $item) {
                $devolucion->productos()->attach($item['producto_id'], [
                    'id'           => \Illuminate\Support\Str::uuid()->toString(),
                    'variante_id'  => $item['variante_id'] ?? null,
                    'cantidad'     => $item['cantidad'],
                    'precio_venta' => $item['precio_venta'],
                ]);
            }

            ActivityLogService::log('Registro de devolución', 'Devoluciones', ['numero' => $devolucion->numero]);
            DB::commit();
            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', 'Devolución registrada: ' . $devolucion->numero);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear devolución', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Devolucion $devolucion): View
    {
        $devolucion->load(['productos', 'venta.cliente.persona', 'user']);
        return view('devolucion.show', compact('devolucion'));
    }

    public function aprobar(Devolucion $devolucion): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $devolucion->update(['estado' => 'Aprobada']);

            // Reintegrar stock al inventario
            foreach ($devolucion->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $varianteId = $producto->pivot->variante_id;

                if ($varianteId) {
                    \App\Models\ProductoVariante::where('id', $varianteId)
                        ->increment('stock', $cantidad);
                } elseif ($producto->inventario) {
                    $producto->inventario->increment('cantidad', $cantidad);
                }
            }

            ActivityLogService::log('Aprobación de devolución', 'Devoluciones', ['id' => $devolucion->id]);
            DB::commit();
            return back()->with('success', 'Devolución aprobada y stock actualizado');
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function rechazar(Devolucion $devolucion): RedirectResponse
    {
        $devolucion->update(['estado' => 'Rechazada']);
        ActivityLogService::log('Rechazo de devolución', 'Devoluciones', ['id' => $devolucion->id]);
        return back()->with('success', 'Devolución rechazada');
    }
}
