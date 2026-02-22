<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductoVarianteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-producto|crear-producto|editar-producto', ['only' => ['index']]);
        $this->middleware('permission:crear-producto', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-producto', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-producto', ['only' => ['destroy']]);
    }

    public function index(Producto $producto): View
    {
        $variantes = $producto->variantes()->orderBy('talla')->orderBy('color')->get();
        return view('variante.index', compact('producto', 'variantes'));
    }

    public function create(Producto $producto): View
    {
        return view('variante.create', compact('producto'));
    }

    public function store(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'talla'        => 'nullable|string|max:20',
            'color'        => 'nullable|string|max:50',
            'sku'          => 'nullable|string|max:100|unique:producto_variantes,sku',
            'stock'        => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'precio'       => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $variante = $producto->variantes()->create($request->only(
                'talla', 'color', 'sku', 'stock', 'stock_minimo', 'precio'
            ));

            ActivityLogService::log('Creación de variante', 'Variantes', $request->all());
            DB::commit();
            return redirect()->route('productos.variantes.index', $producto)
                ->with('success', 'Variante creada correctamente');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear variante', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al crear la variante: ' . $e->getMessage());
        }
    }

    public function edit(Producto $producto, ProductoVariante $variante): View
    {
        return view('variante.edit', compact('producto', 'variante'));
    }

    public function update(Request $request, Producto $producto, ProductoVariante $variante): RedirectResponse
    {
        $request->validate([
            'talla'        => 'nullable|string|max:20',
            'color'        => 'nullable|string|max:50',
            'sku'          => 'nullable|string|max:100|unique:producto_variantes,sku,' . $variante->id,
            'stock'        => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'precio'       => 'nullable|numeric|min:0',
            'estado'       => 'required|in:0,1',
        ]);

        DB::beginTransaction();
        try {
            $variante->update($request->only(
                'talla', 'color', 'sku', 'stock', 'stock_minimo', 'precio', 'estado'
            ));

            ActivityLogService::log('Edición de variante', 'Variantes', $request->all());
            DB::commit();
            return redirect()->route('productos.variantes.index', $producto)
                ->with('success', 'Variante actualizada');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al actualizar variante', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Producto $producto, ProductoVariante $variante): RedirectResponse
    {
        try {
            $variante->update(['estado' => 0]);
            ActivityLogService::log('Desactivación de variante', 'Variantes', ['id' => $variante->id]);
            return redirect()->route('productos.variantes.index', $producto)
                ->with('success', 'Variante desactivada');
        } catch (Throwable $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
