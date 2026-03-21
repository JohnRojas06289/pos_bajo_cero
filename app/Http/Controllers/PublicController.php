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

        // Group by familia_id so variants appear as one card
        $all = $query->orderBy('nombre')->get();
        $families = $all->groupBy(fn($p) => $p->familia_id ?? ('solo_' . $p->id));
        $groups = $families->map(fn($variants) => [
            'main'        => $variants->first(),
            'variants'    => $variants->sortBy(fn($v) => $v->presentacione?->sigla ?? ''),
            'total_stock' => $variants->sum(fn($v) => $v->inventario?->cantidad ?? 0),
        ])->values();

        $perPage = 12;
        $page    = $request->input('page', 1);
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $groups->slice(($page - 1) * $perPage, $perPage)->values(),
            $groups->count(),
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

        $featuredProducts = Producto::with(['inventario', 'marca.caracteristica'])
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
