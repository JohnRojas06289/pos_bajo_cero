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
            $tz          = 'America/Bogota';
            $fechaInicio = $request->input('fecha_inicio', Carbon::now($tz)->format('Y-m-d'));
            $fechaFin    = $request->input('fecha_fin',    Carbon::now($tz)->format('Y-m-d'));
            $rangeStart  = Carbon::createFromFormat('Y-m-d', $fechaInicio, $tz)->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
            $rangeEnd    = Carbon::createFromFormat('Y-m-d', $fechaFin,    $tz)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');

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
            $nowBogota    = Carbon::now($tz);
            $ventasSemana = Venta::whereBetween('created_at', [
                $nowBogota->copy()->startOfWeek()->setTimezone('UTC'),
                $nowBogota->copy()->endOfWeek()->setTimezone('UTC'),
            ])->sum('total');
            $ventasMes  = Venta::whereBetween('created_at', [
                $nowBogota->copy()->startOfMonth()->setTimezone('UTC'),
                $nowBogota->copy()->endOfMonth()->setTimezone('UTC'),
            ])->sum('total');
            $ventasYear = Venta::whereBetween('created_at', [
                $nowBogota->copy()->startOfYear()->setTimezone('UTC'),
                $nowBogota->copy()->endOfYear()->setTimezone('UTC'),
            ])->sum('total');

            $unidadesVendidas    = DB::table('producto_venta')->sum('cantidad');
            $ventasEfectivo      = Venta::where('metodo_pago', 'EFECTIVO')->sum('total');
            $ventasTransferencia = Venta::whereIn('metodo_pago', ['NEQUI', 'DAVIPLATA', 'TRANSFERENCIA'])->sum('total');

            // ── Gastos (compras) del período ─────────────────────────────────
            $gastosPeriodo  = DB::table('compras')->whereBetween('created_at', [$rangeStart, $rangeEnd])->sum('total');
            $gastosMes      = Compra::whereBetween('created_at', [
                $nowBogota->copy()->startOfMonth()->setTimezone('UTC'),
                $nowBogota->copy()->endOfMonth()->setTimezone('UTC'),
            ])->sum('total');
            $gastosYear     = Compra::whereBetween('created_at', [
                $nowBogota->copy()->startOfYear()->setTimezone('UTC'),
                $nowBogota->copy()->endOfYear()->setTimezone('UTC'),
            ])->sum('total');
            $utilidadBruta  = $ventasPeriodo - $gastosPeriodo;
            $margenPct      = $ventasPeriodo > 0 ? round(($utilidadBruta / $ventasPeriodo) * 100, 1) : 0;

            // Gastos por día en el período (para gráfica)
            $gastosPorDia   = DB::table('compras')
                ->selectRaw("DATE(created_at) as fecha, SUM(total) as total, COUNT(*) as cantidad")
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('fecha', 'asc')
                ->get()->toArray();

            // Últimas compras del período
            $ultimasCompras = Compra::with(['proveedore', 'user'])
                ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                ->latest()
                ->limit(10)
                ->get();

            $totalClientes  = Cliente::count();
            $totalProductos = Producto::count();
            $totalCompras   = Compra::count();
            $totalUsuarios  = User::count();

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

            // ── Últimas transacciones (sin filtro de fecha para siempre mostrar las más recientes) ──
            $ultimasVentas = Venta::with(['user', 'cliente.persona', 'productos'])
                ->latest()
                ->limit(20)
                ->get();

            // ── Stock bajo ────────────────────────────────────────────────────
            $productosStockBajo = DB::table('productos')
                ->join('inventario', 'productos.id', '=', 'inventario.producto_id')
                ->where('inventario.cantidad', '<', 10)
                ->where('inventario.cantidad', '>=', 0)
                ->orderBy('inventario.cantidad')
                ->select('productos.nombre', 'inventario.cantidad')
                ->limit(10)->get();

            return response()
                ->view('panel.estadisticas', compact(
                'fechaInicio', 'fechaFin',
                'ventasPeriodo', 'transaccionesPeriodo', 'ticketPromedioPeriodo',
                'ventasSemana', 'ventasMes', 'ventasYear',
                'unidadesVendidas', 'ventasEfectivo', 'ventasTransferencia',
                'totalClientes', 'totalProductos', 'totalCompras', 'totalUsuarios',
                'totalVentasPorDia', 'pagosPorMetodo',
                'top5MasVendidos', 'top5MenosVendidos',
                'ultimasVentas', 'productosStockBajo',
                'gastosPeriodo', 'gastosMes', 'gastosYear',
                'utilidadBruta', 'margenPct', 'gastosPorDia', 'ultimasCompras',
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');

        } catch (\Exception $e) {
            Log::error('Error en EstadisticasController', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->route('panel')->with('error', 'Error al cargar las estadísticas. Intenta de nuevo.');
        }
    }
}
