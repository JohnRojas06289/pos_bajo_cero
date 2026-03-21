<?php

namespace App\Http\Middleware;

use App\Models\Producto;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProductoInicializado
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $producto = Producto::with('inventario')->findOrfail($request->producto_id);
        if ($producto->inventario !== null) {
            return redirect()->route('productos.index')->with('error', 'Este producto ya tiene inventario inicializado');
        }
        return $next($request);
    }
}
