<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;

class PublicController extends Controller
{
    public function home()
    {
        $featuredProducts = Producto::with(['categoria.caracteristica', 'marca.caracteristica', 'presentacione', 'inventario'])
            ->where('estado', 1)
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('featuredProducts'));
    }

    public function collection(Request $request)
    {
        $query = Producto::with(['categoria.caracteristica', 'marca.caracteristica', 'presentacione', 'inventario'])
            ->where('estado', 1);

        if ($request->filled('categoria') && $request->categoria !== 'all') {
            $query->whereHas('categoria.caracteristica', function ($q) use ($request) {
                $q->where('nombre', $request->categoria);
            });
        }

        if ($request->filled('marca') && $request->marca !== 'all') {
            $query->whereHas('marca.caracteristica', function ($q) use ($request) {
                $q->where('nombre', $request->marca);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%');
            });
        }

        $products   = $query->latest()->paginate(12)->withQueryString();
        $categorias = Categoria::with('caracteristica')
            ->whereHas('caracteristica', fn ($q) => $q->where('estado', 1))
            ->get();
        $marcas = Marca::with('caracteristica')
            ->whereHas('caracteristica', fn ($q) => $q->where('estado', 1))
            ->get();

        return view('public.collection', compact('products', 'categorias', 'marcas'));
    }

    public function show($id)
    {
        $product = Producto::with(['categoria.caracteristica', 'marca.caracteristica', 'presentacione', 'inventario'])
            ->where('estado', 1)
            ->findOrFail($id);

        $relatedProducts = Producto::with(['inventario', 'marca.caracteristica'])
            ->where('categoria_id', $product->categoria_id)
            ->where('id', '!=', $id)
            ->where('estado', 1)
            ->latest()
            ->take(4)
            ->get();

        return view('public.show', compact('product', 'relatedProducts'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function about()
    {
        return view('public.about');
    }
}
