<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('ver-estadisticas');

        try {
            // ── Filtros de fecha ──────────────────────────────────────────────
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(29)->format('Y-m-d'));
            $fechaFin    = $request->input('fecha_fin',    Carbon::now()->format('Y-m-d'));
            $rangeStart  = $fechaInicio . ' 00:00:00';
            $rangeEnd    = $fechaFin    . ' 23:59:59';

            // ── KPIs del periodo filtrado ─────────────────────────────────────
            $ventasPeriodo = DB::table('ventas')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->sum('total');
            $transaccionesPeriodo = DB::table('ventas')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->count();
            $ticketPromedioPeriodo = $transaccionesPeriodo > 0
                ? round($ventasPeriodo / $transaccionesPeriodo) : 0;

            // ── KPIs acumulados ───────────────────────────────────────────────
            $ventasSemana = Venta::whereBetween('created_at', [
                Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()
            ])->sum('total');
            $ventasMes  = Venta::whereMonth('created_at', Carbon::now()->month)
                               ->whereYear('created_at', Carbon::now()->year)->sum('total');
            $ventasYear = Venta::whereYear('created_at', Carbon::now()->year)->sum('total');

            $unidadesVendidas    = DB::table('producto_venta')->sum('cantidad');
            $ventasEfectivo      = Venta::where('metodo_pago', 'EFECTIVO')->sum('total');
            $ventasTransferencia = Venta::whereIn('metodo_pago', ['NEQUI', 'DAVIPLATA', 'TRANSFERENCIA'])->sum('total');

            [$totalClientes, $totalProductos, $totalCompras, $totalUsuarios] = Cache::remember(
                'dashboard_counts', 300, fn () => [
                    Cliente::count(),
                    Producto::count(),
                    Compra::count(),
                    User::count(),
                ]
            );

            // ── Gráfica 1: Ventas por día ─────────────────────────────────────
            $totalVentasPorDia = DB::table('ventas')
                ->selectRaw('DATE(created_at) as fecha, SUM(total) as total, COUNT(*) as cantidad')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('fecha', 'asc')
                ->get()->toArray();

            // ── Gráfica 2: Métodos de pago ────────────────────────────────────
            $pagosPorMetodo = DB::table('ventas')
                ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->groupBy('metodo_pago')
                ->get();

            // ── Gráficas 3 & 4: Top 5 más/menos vendidos ─────────────────────
            $baseTop = DB::table('producto_venta')
                ->join('ventas',    'producto_venta.venta_id',    '=', 'ventas.id')
                ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
                ->select('productos.nombre', DB::raw('SUM(producto_venta.cantidad) as total_vendido'))
                ->whereBetween('ventas.created_at', [$rangeStart, $rangeEnd])
                ->groupBy('productos.id', 'productos.nombre');

            $top5MasVendidos   = (clone $baseTop)->orderByDesc('total_vendido')->limit(5)->get();
            $top5MenosVendidos = (clone $baseTop)->orderBy('total_vendido')->limit(5)->get();

            // ── Últimas transacciones ─────────────────────────────────────────
            $ultimasVentas = Venta::with(['user', 'cliente.persona', 'productos'])
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->latest()
                ->limit(15)
                ->get();

            // ── Stock bajo ────────────────────────────────────────────────────
            $productosStockBajo = DB::table('productos')
                ->join('inventario', 'productos.id', '=', 'inventario.producto_id')
                ->where('inventario.cantidad', '<', 10)
                ->where('inventario.cantidad', '>=', 0)
                ->orderBy('inventario.cantidad')
                ->select('productos.nombre', 'inventario.cantidad')
                ->limit(10)->get();

            return view('panel.estadisticas', compact(
                'fechaInicio', 'fechaFin',
                'ventasPeriodo', 'transaccionesPeriodo', 'ticketPromedioPeriodo',
                'ventasSemana', 'ventasMes', 'ventasYear',
                'unidadesVendidas', 'ventasEfectivo', 'ventasTransferencia',
                'totalClientes', 'totalProductos', 'totalCompras', 'totalUsuarios',
                'totalVentasPorDia', 'pagosPorMetodo',
                'top5MasVendidos', 'top5MenosVendidos',
                'ultimasVentas', 'productosStockBajo',
            ));

        } catch (\Exception $e) {
            Log::error('Error en EstadisticasController', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->route('panel')->with('error', 'Error al cargar las estadísticas. Intenta de nuevo.');
        }
    }
}
