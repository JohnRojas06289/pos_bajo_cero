<?php

namespace App\Http\Controllers;

use App\Enums\TipoTransaccionEnum;
use App\Http\Requests\StoreInventarioRequest;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Models\Producto;
use App\Models\Ubicacione;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InventarioController extends Controller
{
    function __construct()
    {
        $this->middleware('check_producto_inicializado', ['only' => ['create', 'store']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $categorias = \App\Models\Categoria::with('caracteristica')->get();
        
        $productos = Producto::with(['inventario', 'variantes', 'presentacione', 'categoria.caracteristica'])
            ->when($request->categoria_id, function($query, $categoria_id) {
                return $query->where('categoria_id', $categoria_id);
            })
            ->orderBy('nombre', 'asc')
            ->get();
            
        return view('inventario.index', compact('productos', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $producto = Producto::findOrfail($request->producto_id);
        $ubicaciones = Ubicacione::all();
        return view('inventario.create', compact('producto', 'ubicaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventarioRequest $request, Kardex $kardex): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $kardex->crearRegistro($request->validated(), TipoTransaccionEnum::Apertura);
            Inventario::create($request->safe()->except(['costo_unitario', 'precio_venta', 'precio_al_por_mayor']));

            // Update product sale price
            $producto = Producto::findOrFail($request->producto_id);
            $producto->update(['precio' => $request->precio_venta]);
            if ($request->filled('precio_al_por_mayor')) {
                $producto->update(['precio_al_por_mayor' => $request->precio_al_por_mayor]);
            }

            DB::commit();
            ActivityLogService::log('Inicialiación de producto', 'Productos', $request->validated());
            return redirect()->route('productos.index')->with('success', 'Producto inicializado');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al inicializar el producto', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Ups, algo falló');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $inventario = Inventario::with('producto.variantes')->findOrFail($id);
        $producto = $inventario->producto;
        
        // Fetch last cost from Kardex
        $ultimoKardex = Kardex::where('producto_id', $producto->id)->latest('id')->first();
        $costo_unitario = $ultimoKardex ? $ultimoKardex->costo_unitario : 0;

        return view('inventario.edit', compact('inventario', 'producto', 'costo_unitario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreInventarioRequest $request, string $id)
    {
        $inventario = Inventario::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Updated Product Price
            $producto = $inventario->producto;
            if ($request->has('precio_venta')) {
                $producto->update(['precio' => $request->precio_venta]);
            }
            if ($request->filled('precio_al_por_mayor')) {
                $producto->update(['precio_al_por_mayor' => $request->precio_al_por_mayor]);
            }

            // Update Inventory (excluding fields not in table)
            // We use except() because validated() returns fields like costo_unitario/precio_venta that don't exist in 'inventario' table
            $data = $request->safe()->except(['costo_unitario', 'precio_venta']);
            $inventario->update($data);

            // Sincronizar variante.stock con la nueva cantidad (fuente de verdad del POS)
            $nuevaCantidad = (int) $request->cantidad;
            $variantes = \App\Models\Variante::where('producto_id', $producto->id)
                ->orderBy('stock', 'desc')
                ->get();

            if ($variantes->count() === 1) {
                $variantes->first()->update(['stock' => $nuevaCantidad]);
            } elseif ($variantes->count() > 1) {
                $stockActual = $variantes->sum('stock');
                $diff = $nuevaCantidad - $stockActual;
                if ($diff !== 0) {
                    // Aplicar la diferencia a la variante con más stock (la primera, ya ordenada desc)
                    $objetivo = $variantes->first();
                    $nuevoStock = max(0, $objetivo->stock + $diff);
                    $objetivo->update(['stock' => $nuevoStock]);
                }
            }
            
            // Actualizar costo en Kardex (último registro)
            if ($request->has('costo_unitario')) {
                $ultimoKardex = \App\Models\Kardex::where('producto_id', $producto->id)->latest('id')->first();
                if ($ultimoKardex) {
                    $ultimoKardex->update(['costo_unitario' => $request->costo_unitario]);
                }
            }

            DB::commit();
            ActivityLogService::log('Actualización de inventario', 'Inventario', $request->validated());
            return redirect()->route('inventario.index')->with('success', 'Inventario actualizado');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al actualizar el inventario', ['error' => $e->getMessage()]);
            return redirect()->route('inventario.index')->with('error', 'Ups, algo falló');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inventario = Inventario::findOrFail($id);
        DB::beginTransaction();
        try {
            $inventario->delete();
            DB::commit();
            ActivityLogService::log('Eliminación de inventario', 'Inventario', ['id' => $id]);
            return redirect()->route('inventario.index')->with('success', 'Inventario eliminado');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al eliminar el inventario', ['error' => $e->getMessage()]);
            return redirect()->route('inventario.index')->with('error', 'Ups, algo falló');
        }
    }
}
