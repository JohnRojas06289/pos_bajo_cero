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

            // ── Ventas por cliente del día (con productos) ────────────────────
            $ventasPorClienteHoy = Venta::with(['cliente.persona', 'productos', 'user'])
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->get();

            return view('panel.index', compact(
                'ventasHoy', 'transaccionesHoy', 'ticketPromedioHoy',
                'efectivoHoy', 'nequiHoy', 'daviplataHoy', 'transferenciaHoy',
                'ventasPorClienteHoy',
            ));

        } catch (\Exception $e) {
            return response("Error en Dashboard: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
        }
    }

    public function kpis(): \Illuminate\Http\JsonResponse
    {
        if (!Auth::check() || !Auth::user()->can('ver-panel')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ventasHoy        = Venta::whereDate('created_at', Carbon::today())->sum('total');
        $transaccionesHoy = Venta::whereDate('created_at', Carbon::today())->count();
        $efectivoHoy      = Venta::where('metodo_pago', 'EFECTIVO')->whereDate('created_at', Carbon::today())->sum('total');
        $nequiHoy         = Venta::where('metodo_pago', 'NEQUI')->whereDate('created_at', Carbon::today())->sum('total');
        $daviplataHoy     = Venta::where('metodo_pago', 'DAVIPLATA')->whereDate('created_at', Carbon::today())->sum('total');
        $transferenciaHoy = Venta::where('metodo_pago', 'TRANSFERENCIA')->whereDate('created_at', Carbon::today())->sum('total');

        $ventas = Venta::with(['cliente.persona', 'productos', 'user'])
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->get()
            ->map(fn($v) => [
                'hora'      => Carbon::parse($v->created_at)->format('H:i'),
                'cliente'   => $v->cliente?->persona?->razon_social ?? 'Cliente General',
                'metodo'    => $v->metodo_pago,
                'total'     => (float) $v->total,
                'vendedor'  => $v->user?->name ?? 'N/A',
                'numero'    => $v->numero_venta ?? null,
                'fecha'     => Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                'productos' => $v->productos->map(fn($p) => [
                    'nombre'   => $p->nombre,
                    'cantidad' => $p->pivot->cantidad,
                    'precio'   => (float) $p->pivot->precio_venta,
                    'subtotal' => (float) ($p->pivot->cantidad * $p->pivot->precio_venta),
                ]),
            ]);

        return response()->json([
            'ventasHoy'         => (float) $ventasHoy,
            'transaccionesHoy'  => (int)   $transaccionesHoy,
            'ticketPromedioHoy' => $transaccionesHoy > 0 ? round($ventasHoy / $transaccionesHoy) : 0,
            'efectivoHoy'       => (float) $efectivoHoy,
            'nequiHoy'          => (float) $nequiHoy,
            'daviplataHoy'      => (float) $daviplataHoy,
            'transferenciaHoy'  => (float) $transferenciaHoy,
            'ventas'            => $ventas,
        ]);
    }
}
