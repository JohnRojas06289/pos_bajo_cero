<?php

namespace App\Http\Controllers;

use App\Enums\MetodoPagoEnum;
use App\Events\CreateVentaDetalleEvent;
use App\Events\CreateVentaEvent;
use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\ActivityLogService;
use App\Services\ComprobanteService;
use App\Services\EmpresaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ventaController extends Controller
{
    protected EmpresaService $empresaService;

    function __construct(EmpresaService $empresaService)
    {
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', ['only' => ['index']]);
        $this->middleware('permission:crear-venta', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-venta', ['only' => ['show']]);
        //$this->middleware('permission:eliminar-venta', ['only' => ['destroy']]);
        $this->middleware('check-caja-aperturada-user', ['only' => ['create', 'store']]);
        $this->middleware('check-show-venta-user', ['only' => ['show']]);
        $this->empresaService = $empresaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $ventas = Venta::with(['comprobante', 'cliente.persona', 'user'])
            ->latest()
            ->paginate(20);

        return view('venta.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ComprobanteService $comprobanteService): View|RedirectResponse
    {
        try {
            // Verificar que existe una empresa
            $empresa = $this->empresaService->obtenerEmpresa();

            // Verificar que existen clientes
            $clientes = Cliente::whereHas('persona', function ($query) {
                $query->where('estado', 1);
            })->get();

            if ($clientes->isEmpty()) {
                return redirect()->route('panel')
                    ->with('error', 'Debe crear al menos un cliente antes de realizar ventas. Vaya a Clientes > Nuevo Cliente.');
            }

            // Cargar variantes de productos activos (una fila por variante)
            $productos = DB::table('variantes as v')
                ->join('productos as p', 'v.producto_id', '=', 'p.id')
                ->leftJoin('presentaciones as pr', 'v.presentacione_id', '=', 'pr.id')
                ->leftJoin('caracteristicas as cp', 'cp.id', '=', 'pr.caracteristica_id')
                ->select(
                    'v.id as variante_id',
                    'v.producto_id',
                    'v.color',
                    'v.stock as cantidad',
                    'v.img_path as variante_img',
                    DB::raw("COALESCE(pr.sigla, '') as sigla"),
                    DB::raw("COALESCE(cp.nombre, '') as talla_nombre"),
                    'p.nombre',
                    'p.codigo',
                    'p.id as producto_uuid',
                    'p.precio',
                    'p.img_path',
                    'p.categoria_id',
                    'p.genero'
                )
                ->where('p.estado', 1)
                ->get();

            $categorias = Cache::remember('categorias_activas', 3600, function () {
                return Categoria::with('caracteristica')
                    ->whereHas('caracteristica', function ($query) {
                        $query->where('estado', 1);
                    })->get();
            });

            $comprobantes = $comprobanteService->obtenerComprobantes();
            $optionsMetodoPago = MetodoPagoEnum::cases();

            return view('venta.create', compact(
                'productos',
                'categorias',
                'clientes',
                'comprobantes',
                'optionsMetodoPago',
                'empresa'
            ));
        } catch (\Throwable $e) {
            Log::error('Error al cargar la vista de crear venta', ['error' => $e->getMessage()]);
            return redirect()->route('panel')
                ->with('error', 'Error al cargar el punto de venta: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentaRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            //Llenar mi tabla venta
            $venta = Venta::create($request->validated());

            //Llenar mi tabla venta_producto
            //1. Recuperar los arrays
            $arrayProducto_id  = $request->get('arrayidproducto');
            $arrayVarianteId   = $request->get('arrayvariante_id', []);
            $arrayCantidad     = $request->get('arraycantidad');
            $arrayPrecioVenta  = $request->get('arrayprecioventa');

            if (empty($arrayProducto_id)) {
                DB::rollBack();
                return redirect()->route('ventas.create')->with('error', 'Debe agregar al menos un producto a la venta.');
            }

            //2. Realizar el llenado
            $siseArray = count($arrayProducto_id);
            $cont = 0;

            while ($cont < $siseArray) {
                $venta->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                        'id' => \Illuminate\Support\Str::uuid()->toString(),
                        'cantidad' => $arrayCantidad[$cont],
                        'precio_venta' => $arrayPrecioVenta[$cont],
                    ]
                ]);

                // Despachar evento — incluye variante_id para descontar stock correcto
                CreateVentaDetalleEvent::dispatch(
                    $venta,
                    $arrayProducto_id[$cont],
                    $arrayCantidad[$cont],
                    $arrayPrecioVenta[$cont],
                    $arrayVarianteId[$cont] ?? null
                );

                $cont++;
            }

            //Despachar evento
            CreateVentaEvent::dispatch($venta);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear la venta', ['error' => $e->getMessage()]);
            return redirect()->route('ventas.create')->with('error', 'Ups, algo falló: ' . $e->getMessage());
        }

        // Log fuera de la transacción para no mezclar fallos del log con rollback de la venta
        try {
            ActivityLogService::log('Creación de una venta', 'Ventas', $request->validated());
        } catch (Throwable $e) {
            Log::warning('No se pudo registrar el activity log de venta', ['error' => $e->getMessage()]);
        }

        return redirect()->route('ventas.create')
            ->with('success', 'Venta registrada');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta): View
    {
        $empresa =  $this->empresaService->obtenerEmpresa();
        return view('venta.show', compact('venta', 'empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /* Venta::where('id', $id)
            ->update([
                'estado' => 0
            ]);

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada');*/
    }
}
