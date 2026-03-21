<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{
    // ── Pública ────────────────────────────────────────────

    public function index(Request $request)
    {
        $products = Producto::with(['categoria.caracteristica', 'marca.caracteristica', 'variantes'])
            ->where('estado', 1)
            ->latest()
            ->get();

        $preselected = null;
        if ($request->filled('producto')) {
            $preselected = Producto::with(['variantes', 'marca.caracteristica'])
                ->where('estado', 1)
                ->find($request->producto);
        }

        return view('public.reservar', compact('products', 'preselected'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:120',
            'telefono'    => 'required|string|max:30',
            'email'       => 'nullable|email|max:120',
            'notas'       => 'nullable|string|max:500',
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:productos,id',
            'items.*.qty' => 'required|integer|min:1|max:99',
        ], [
            'items.required' => 'Debes seleccionar al menos un producto.',
            'nombre.required' => 'El nombre es requerido.',
            'telefono.required' => 'El teléfono es requerido.',
        ]);

        // Build product list
        $productosReserva = [];
        $total = 0;

        foreach ($request->items as $item) {
            $producto = Producto::with('marca.caracteristica')->find($item['id']);
            if (!$producto) continue;

            $subtotal = $producto->precio * $item['qty'];
            $total   += $subtotal;

            $productosReserva[] = [
                'id'       => $producto->id,
                'nombre'   => $producto->nombre,
                'marca'    => $producto->marca->caracteristica->nombre ?? '',
                'precio'   => $producto->precio,
                'cantidad' => $item['qty'],
                'subtotal' => $subtotal,
            ];
        }

        if (empty($productosReserva)) {
            return back()->withErrors(['items' => 'Ningún producto seleccionado es válido.'])->withInput();
        }

        $reserva = Reserva::create([
            'nombre'    => $request->nombre,
            'telefono'  => $request->telefono,
            'email'     => $request->email,
            'productos' => $productosReserva,
            'total'     => $total,
            'notas'     => $request->notas,
            'estado'    => 'pendiente',
        ]);

        // Build WhatsApp message
        $numero = config('services.whatsapp.numero', env('WHATSAPP_NUMERO', '573001234567'));

        $mensaje  = "¡Hola! Quiero hacer una reserva en *Bajo Cero* 🧊\n\n";
        $mensaje .= "*Mis datos:*\n";
        $mensaje .= "• Nombre: {$request->nombre}\n";
        if ($request->email) $mensaje .= "• Email: {$request->email}\n";
        $mensaje .= "\n*Productos que me interesan:*\n";

        foreach ($productosReserva as $p) {
            $mensaje .= "• {$p['nombre']} x{$p['cantidad']} — \$" . number_format($p['subtotal'], 0) . "\n";
        }

        $mensaje .= "\n*Total estimado: \$" . number_format($total, 0) . " COP*";
        if ($request->notas) {
            $mensaje .= "\n\n*Notas:* {$request->notas}";
        }

        $urlWsp = "https://wa.me/{$numero}?text=" . urlencode($mensaje);

        return redirect($urlWsp);
    }

    // ── Admin ──────────────────────────────────────────────

    public function adminIndex(Request $request)
    {
        $query = Reserva::latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('telefono', 'like', "%{$s}%");
            });
        }

        $reservas        = $query->paginate(15)->withQueryString();
        $pendientesCount = Reserva::where('estado', 'pendiente')->count();

        return view('admin.reservas.index', compact('reservas', 'pendientesCount'));
    }

    public function updateEstado(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,contactado,confirmada,cancelada',
        ]);

        $reserva->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado actualizado correctamente.');
    }
}
