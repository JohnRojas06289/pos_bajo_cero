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
        $featuredProducts = Producto::with(['categoria.caracteristica', 'marca.caracteristica', 'variantes'])
            ->where('estado', 1)
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('featuredProducts'));
    }

    public function collection(Request $request)
    {
        $query = Producto::with(['categoria.caracteristica', 'marca.caracteristica', 'variantes.presentacione'])
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

        $all = $query->orderBy('nombre')->get();

        $perPage = 12;
        $page    = $request->input('page', 1);
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $all->slice(($page - 1) * $perPage, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
        $product = Producto::with(['categoria.caracteristica', 'marca.caracteristica', 'variantes.presentacione'])
            ->where('estado', 1)
            ->findOrFail($id);

        $relatedProducts = Producto::with(['variantes', 'marca.caracteristica'])
            ->where('categoria_id', $product->categoria_id)
            ->where('id', '!=', $id)
            ->where('estado', 1)
            ->latest()
            ->take(4)
            ->get();

        $featuredProducts = Producto::with(['variantes', 'marca.caracteristica'])
            ->where('estado', 1)
            ->where('id', '!=', $id)
            ->latest()
            ->take(4)
            ->get();

        return view('public.show', compact('product', 'relatedProducts', 'featuredProducts'));
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
