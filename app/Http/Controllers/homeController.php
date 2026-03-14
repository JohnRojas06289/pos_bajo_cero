<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class homeController extends Controller
{
    public function index(Request $request): View|RedirectResponse|\Illuminate\Http\Response
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        if (!Auth::user()->can('ver-panel')) {
            return redirect()->route('ventas.create');
        }

        try {
            // ── Filtros de fecha ──────────────────────────────────────────────
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(29)->format('Y-m-d'));
            $fechaFin    = $request->input('fecha_fin',    Carbon::now()->format('Y-m-d'));
            $rangeStart  = $fechaInicio . ' 00:00:00';
            $rangeEnd    = $fechaFin    . ' 23:59:59';

            // ── KPIs del día ──────────────────────────────────────────────────
            $ventasHoy         = Venta::whereDate('created_at', Carbon::today())->sum('total');
            $transaccionesHoy  = Venta::whereDate('created_at', Carbon::today())->count();
            $ticketPromedioHoy = $transaccionesHoy > 0 ? round($ventasHoy / $transaccionesHoy) : 0;
            $efectivoHoy       = Venta::where('metodo_pago', 'EFECTIVO')
                                      ->whereDate('created_at', Carbon::today())->sum('total');
            $nequiHoy          = Venta::where('metodo_pago', 'NEQUI')
                                      ->whereDate('created_at', Carbon::today())->sum('total');
            $daviplataHoy      = Venta::where('metodo_pago', 'DAVIPLATA')
                                      ->whereDate('created_at', Carbon::today())->sum('total');
            $transferenciaHoy  = Venta::where('metodo_pago', 'TRANSFERENCIA')
                                      ->whereDate('created_at', Carbon::today())->sum('total');

            // ── KPIs globales (cached 5 min) ──────────────────────────────────
            $ventasSemana = Venta::whereBetween('created_at', [
                Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()
            ])->sum('total');
            $ventasMes  = Venta::whereMonth('created_at', Carbon::now()->month)
                               ->whereYear('created_at', Carbon::now()->year)->sum('total');
            $ventasYear = Venta::whereYear('created_at', Carbon::now()->year)->sum('total');

            [$totalClientes, $totalProductos, $totalCompras, $totalUsuarios] = Cache::remember(
                'dashboard_counts', 300, fn () => [
                    Cliente::count(),
                    Producto::count(),
                    Compra::count(),
                    User::count(),
                ]
            );

            // ── Ventas por cliente del día (con productos) ────────────────────
            $ventasPorClienteHoy = Venta::with(['cliente.persona', 'productos', 'user'])
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->get();

            // ── Últimas transacciones (con productos para modal detalle) ───────
            $ultimasVentas = Venta::with(['user', 'cliente.persona', 'productos'])
                ->latest()
                ->limit(10)
                ->get();

            // ── KPIs del periodo filtrado ─────────────────────────────────────
            $ventasPeriodo = DB::table('ventas')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->sum('total');
            $transaccionesPeriodo = DB::table('ventas')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->count();
            $ticketPromedioPeriodo = $transaccionesPeriodo > 0
                ? round($ventasPeriodo / $transaccionesPeriodo) : 0;

            // ── Gráfica 1: Ventas por día (filtrado) ──────────────────────────
            $totalVentasPorDia = DB::table('ventas')
                ->selectRaw('DATE(created_at) as fecha, SUM(total) as total, COUNT(*) as cantidad')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('fecha', 'asc')
                ->get()->toArray();

            // ── Gráfica 2: Distribución por método de pago (filtrado) ─────────
            $pagosPorMetodo = DB::table('ventas')
                ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->groupBy('metodo_pago')
                ->get();

            // ── Gráfica 3 & 4: Top 5 más/menos vendidos (filtrado) ────────────
            $baseTop = DB::table('producto_venta')
                ->join('ventas',   'producto_venta.venta_id',   '=', 'ventas.id')
                ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
                ->select('productos.nombre', DB::raw('SUM(producto_venta.cantidad) as total_vendido'))
                ->whereBetween('ventas.created_at', [$rangeStart, $rangeEnd])
                ->groupBy('productos.id', 'productos.nombre');

            $top5MasVendidos   = (clone $baseTop)->orderByDesc('total_vendido')->limit(5)->get();
            $top5MenosVendidos = (clone $baseTop)->orderBy('total_vendido')->limit(5)->get();

            // ── Inventario ────────────────────────────────────────────────────
            $productosMasStock = DB::table('productos')
                ->join('inventario', 'productos.id', '=', 'inventario.producto_id')
                ->orderByDesc('inventario.cantidad')
                ->select('productos.nombre', 'inventario.cantidad')
                ->limit(5)->get();

            $productosMenosStock = DB::table('productos')
                ->join('inventario', 'productos.id', '=', 'inventario.producto_id')
                ->where('inventario.cantidad', '>', 0)
                ->orderBy('inventario.cantidad')
                ->select('productos.nombre', 'inventario.cantidad')
                ->limit(5)->get();

            // Stock bajo: menos de 10 unidades
            $productosStockBajo = DB::table('productos')
                ->join('inventario', 'productos.id', '=', 'inventario.producto_id')
                ->where('inventario.cantidad', '<', 10)
                ->where('inventario.cantidad', '>=', 0)
                ->orderBy('inventario.cantidad')
                ->select('productos.nombre', 'inventario.cantidad')
                ->limit(10)->get();

            $unidadesVendidas = DB::table('producto_venta')->sum('cantidad');

            // Métodos de pago acumulados (para KPIs generales)
            $ventasEfectivo      = Venta::where('metodo_pago', 'EFECTIVO')->sum('total');
            $ventasTransferencia = Venta::whereIn('metodo_pago', ['NEQUI', 'DAVIPLATA', 'TRANSFERENCIA'])->sum('total');
            $ventasMayoristas    = Venta::whereHas('cliente.persona', fn($q) => $q->whereRaw('es_mayorista = true'))->sum('total');

            // Top 3 para backward compatibility con secciones que los usen
            $productosMasVendidos   = $top5MasVendidos->take(3);
            $productosMenosVendidos = $top5MenosVendidos->take(3);

            return view('panel.index', compact(
                // KPIs del día
                'ventasHoy', 'transaccionesHoy', 'ticketPromedioHoy',
                'efectivoHoy', 'nequiHoy', 'daviplataHoy', 'transferenciaHoy',
                // KPIs globales
                'ventasSemana', 'ventasMes', 'ventasYear',
                'unidadesVendidas', 'ventasEfectivo', 'ventasTransferencia', 'ventasMayoristas',
                'totalClientes', 'totalProductos', 'totalCompras', 'totalUsuarios',
                // Ventas del día
                'ventasPorClienteHoy',
                // Últimas ventas
                'ultimasVentas',
                // Periodo filtrado
                'fechaInicio', 'fechaFin',
                'ventasPeriodo', 'transaccionesPeriodo', 'ticketPromedioPeriodo',
                // Gráficas
                'totalVentasPorDia', 'pagosPorMetodo',
                'top5MasVendidos', 'top5MenosVendidos',
                'productosMasVendidos', 'productosMenosVendidos',
                'productosMasStock', 'productosMenosStock', 'productosStockBajo',
            ));

        } catch (\Exception $e) {
            return response("Error en Dashboard: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
        }
    }
}
