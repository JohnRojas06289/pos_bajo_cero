<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class AgenteIAController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $userId = auth()->id();
        $key    = "agente_ia_{$userId}";

        // Rate limit: 20 mensajes/usuario/hora
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Demasiados mensajes. Intenta en " . ceil($seconds / 60) . " minuto(s)."
            ], 429);
        }
        RateLimiter::hit($key, 3600);

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['reply' => 'El asistente IA no está configurado. Agrega GEMINI_API_KEY al .env.']);
        }

        // Contexto del negocio
        $context = $this->buildContext();

        $systemPrompt = "Eres el asistente del POS Jacket Store, un almacén de ropa en Colombia especializado en chaquetas. " .
            "Ayudas al usuario a navegar el sistema, responder preguntas de inventario, ventas y productos. " .
            "Responde en español colombiano, corto y claro. " .
            "Cuando informes dinero usa formato \$XX.XXX COP. " .
            "Cuando sugieras navegar, incluye la ruta exacta (ej: /admin/ventas, /admin/productos). " .
            "CONTEXTO ACTUAL DEL NEGOCIO:\n{$context}";

        try {
            $response = Http::timeout(15)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}",
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]]
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $request->input('message')]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 400,
                    ]
                ]
            );

            if ($response->failed()) {
                return response()->json(['reply' => 'No pude conectarme al asistente. Intenta de nuevo.']);
            }

            $data  = $response->json();
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sin respuesta.';

            return response()->json(['reply' => trim($reply)]);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Error al procesar tu mensaje. Intenta de nuevo.']);
        }
    }

    private function buildContext(): string
    {
        return Cache::remember('agente_ia_context', 300, function () {
            // Ventas del día
            $ventasHoy    = Venta::whereDate('created_at', Carbon::today())->sum('total');
            $cantVentasHoy = Venta::whereDate('created_at', Carbon::today())->count();

            // Stock bajo (< 10 unidades)
            $stockBajo = DB::table('productos')
                ->join('inventario', 'productos.id', '=', 'inventario.producto_id')
                ->where('inventario.cantidad', '<', 10)
                ->where('inventario.cantidad', '>=', 0)
                ->orderBy('inventario.cantidad')
                ->select('productos.nombre', 'inventario.cantidad')
                ->limit(8)
                ->get();

            // Top 5 más vendidos esta semana
            $topVendidos = DB::table('producto_venta')
                ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
                ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
                ->select('productos.nombre', DB::raw('SUM(producto_venta.cantidad) as total'))
                ->whereBetween('ventas.created_at', [Carbon::now()->startOfWeek(), Carbon::now()])
                ->groupBy('productos.id', 'productos.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            // Total de productos activos
            $totalProductos = Producto::where('estado', 1)->count();

            $ctx  = "Fecha: " . Carbon::now()->format('d/m/Y H:i') . "\n";
            $ctx .= "Ventas hoy: $" . number_format($ventasHoy, 0, ',', '.') . " COP en {$cantVentasHoy} transacciones.\n";
            $ctx .= "Productos activos en catálogo: {$totalProductos}.\n";

            if ($stockBajo->isNotEmpty()) {
                $ctx .= "Stock bajo (<10 uds): ";
                $ctx .= $stockBajo->map(fn($p) => "{$p->nombre} ({$p->cantidad} uds)")->implode(', ') . ".\n";
            }

            if ($topVendidos->isNotEmpty()) {
                $ctx .= "Top vendidos esta semana: ";
                $ctx .= $topVendidos->map(fn($p) => "{$p->nombre} ({$p->total} uds)")->implode(', ') . ".\n";
            }

            return $ctx;
        });
    }
}
