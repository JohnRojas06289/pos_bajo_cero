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
        // Fetch featured products for the home page (e.g., latest 4)
        $featuredProducts = Producto::with(['categoria', 'marca', 'presentacione'])
            ->latest()
            ->take(4)
            ->get();
            
        return view('welcome', compact('featuredProducts'));
    }

    public function collection(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'presentacione']);

        // Filter by Category
        if ($request->has('categoria') && $request->categoria != 'all') {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('nombre', $request->categoria);
            });
        }

        // Filter by Brand
        if ($request->has('marca') && $request->marca != 'all') {
            $query->whereHas('marca', function ($q) use ($request) {
                $q->where('nombre', $request->marca);
            });
        }

        // Search
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(12);
        $categorias = Categoria::all();
        $marcas = Marca::all();

        return view('public.collection', compact('products', 'categorias', 'marcas'));
    }

    public function show($id)
    {
        $product = Producto::with(['categoria', 'marca', 'presentacione', 'inventario'])->findOrFail($id);
        
        // Related products (same category, excluding current)
        $relatedProducts = Producto::where('categoria_id', $product->categoria_id)
            ->where('id', '!=', $id)
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
