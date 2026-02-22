<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-reporte');
    }

    public function rentabilidad(Request $request): View
    {
        $fechaInicio = $request->fecha_inicio ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin    = $request->fecha_fin    ?? now()->format('Y-m-d');

        // Productos más vendidos con margen
        $productos = Producto::select(
                'productos.id',
                'productos.nombre',
                'productos.precio',
                'productos.origen',
                DB::raw('COALESCE(SUM(pv.cantidad), 0) as unidades_vendidas'),
                DB::raw('COALESCE(SUM(pv.cantidad * pv.precio_venta), 0) as ingreso_total'),
                DB::raw('COALESCE(MAX(k.costo_unitario), 0) as ultimo_costo')
            )
            ->leftJoin('producto_venta as pv', function ($join) use ($fechaInicio, $fechaFin) {
                $join->on('pv.producto_id', '=', 'productos.id')
                    ->whereExists(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->select(DB::raw(1))
                          ->from('ventas')
                          ->whereColumn('ventas.id', 'pv.venta_id')
                          ->whereBetween('ventas.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
                    });
            })
            ->leftJoin(DB::raw('(SELECT producto_id, MAX(costo_unitario) as costo_unitario FROM kardexes GROUP BY producto_id) k'), 'k.producto_id', '=', 'productos.id')
            ->where('productos.estado', 1)
            ->groupBy('productos.id', 'productos.nombre', 'productos.precio', 'productos.origen')
            ->orderByDesc('ingreso_total')
            ->get()
            ->map(function ($p) {
                $costoTotal = $p->unidades_vendidas * $p->ultimo_costo;
                $p->margen_bruto = $p->ingreso_total - $costoTotal;
                $p->margen_pct = $p->ingreso_total > 0
                    ? round(($p->margen_bruto / $p->ingreso_total) * 100, 1)
                    : 0;
                return $p;
            });

        // Resumen por origen
        $resumenOrigen = $productos->groupBy('origen')->map(function ($grupo) {
            return [
                'unidades'  => $grupo->sum('unidades_vendidas'),
                'ingresos'  => $grupo->sum('ingreso_total'),
                'margen'    => $grupo->sum('margen_bruto'),
            ];
        });

        // Top 5 más rentables
        $topRentables = $productos->sortByDesc('margen_bruto')->take(5);

        // Productos sin ventas en el período
        $sinVentas = $productos->where('unidades_vendidas', 0)->count();

        return view('reporte.rentabilidad', compact(
            'productos', 'resumenOrigen', 'topRentables', 'sinVentas', 'fechaInicio', 'fechaFin'
        ));
    }
}
