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
            $tz         = 'America/Bogota';
            $hoyStart   = Carbon::now($tz)->startOfDay()->setTimezone('UTC');
            $hoyEnd     = Carbon::now($tz)->endOfDay()->setTimezone('UTC');
            $ventasHoy         = Venta::whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
            $transaccionesHoy  = Venta::whereBetween('created_at', [$hoyStart, $hoyEnd])->count();
            $ticketPromedioHoy = $transaccionesHoy > 0 ? round($ventasHoy / $transaccionesHoy) : 0;
            $efectivoHoy       = Venta::where('metodo_pago', 'EFECTIVO')
                                      ->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
            $nequiHoy          = Venta::where('metodo_pago', 'NEQUI')
                                      ->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
            $daviplataHoy      = Venta::where('metodo_pago', 'DAVIPLATA')
                                      ->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
            $transferenciaHoy  = Venta::whereIn('metodo_pago', ['TRANSFERENCIA', 'VENTA_DIGITAL'])
                                      ->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');

            // ── Ventas por cliente del día (con productos) ────────────────────
            $ventasPorClienteHoy = Venta::with(['cliente.persona', 'productos', 'user'])
                ->whereBetween('created_at', [$hoyStart, $hoyEnd])
                ->latest()
                ->limit(50)
                ->get();

            return view('panel.index', compact(
                'ventasHoy', 'transaccionesHoy', 'ticketPromedioHoy',
                'efectivoHoy', 'nequiHoy', 'daviplataHoy', 'transferenciaHoy',
                'ventasPorClienteHoy',
            ));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en Dashboard', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            return response()->view('errors.500', [], 500);
        }
    }

    public function kpis(): \Illuminate\Http\JsonResponse
    {
        if (!Auth::check() || !Auth::user()->can('ver-panel')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tz               = 'America/Bogota';
        $hoyStart         = Carbon::now($tz)->startOfDay()->setTimezone('UTC');
        $hoyEnd           = Carbon::now($tz)->endOfDay()->setTimezone('UTC');
        $ventasHoy        = Venta::whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
        $transaccionesHoy = Venta::whereBetween('created_at', [$hoyStart, $hoyEnd])->count();
        $efectivoHoy      = Venta::where('metodo_pago', 'EFECTIVO')->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
        $nequiHoy         = Venta::where('metodo_pago', 'NEQUI')->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
        $daviplataHoy     = Venta::where('metodo_pago', 'DAVIPLATA')->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');
        $transferenciaHoy = Venta::whereIn('metodo_pago', ['TRANSFERENCIA', 'VENTA_DIGITAL'])->whereBetween('created_at', [$hoyStart, $hoyEnd])->sum('total');

        $ventas = Venta::with(['cliente.persona', 'productos', 'user'])
            ->whereBetween('created_at', [$hoyStart, $hoyEnd])
            ->latest()
            ->get()
            ->map(fn($v) => [
                'hora'      => Carbon::parse($v->created_at)->setTimezone($tz)->format('H:i'),
                'cliente'   => $v->cliente?->persona?->razon_social ?? 'Cliente General',
                'metodo'    => $v->metodo_pago,
                'total'     => (float) $v->total,
                'vendedor'  => $v->user?->name ?? 'N/A',
                'numero'    => $v->numero_venta ?? null,
                'fecha'     => Carbon::parse($v->created_at)->setTimezone($tz)->format('d/m/Y H:i'),
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
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate')
          ->header('Pragma', 'no-cache');
    }
}
