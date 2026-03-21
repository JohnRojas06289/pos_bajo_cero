<?php

namespace App\Http\Controllers;

use App\Enums\MetodoPagoEnum;
use App\Events\CreateCompraDetalleEvent;
use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Proveedore;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\ActivityLogService;
use App\Services\ComprobanteService;
use App\Services\EmpresaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class compraController extends Controller
{
    protected EmpresaService $empresaService;

    function __construct(EmpresaService $empresaService)
    {
        $this->middleware('permission:ver-compra|crear-compra|mostrar-compra|eliminar-compra', ['only' => ['index']]);
        $this->middleware('permission:crear-compra', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-compra', ['only' => ['show']]);
        //$this->middleware('permission:eliminar-compra', ['only' => ['destroy']]);
        $this->middleware('check-show-compra-user', ['only' => ['show']]);
        $this->empresaService = $empresaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $compras = Compra::with('comprobante', 'proveedore.persona')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('compra.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ComprobanteService $comprobanteService): View
    {
        $proveedores = Proveedore::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();
        $comprobantes = $comprobanteService->obtenerComprobantes();
        $productos = Producto::with(['inventario', 'presentacione.caracteristica'])
            ->where('estado', 1)
            ->get();
        $optionsMetodoPago = MetodoPagoEnum::cases();
        $empresa = $this->empresaService->obtenerEmpresa();

        return view('compra.create', compact(
            'proveedores',
            'comprobantes',
            'productos',
            'optionsMetodoPago',
            'empresa'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompraRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {

            //Llenar tabla compras
            $tmpCompra = new Compra();
            $comprobantePath = $request->hasFile('file_comprobante')
                ? $tmpCompra->handleUploadFile($request->file_comprobante)
                : null;

            $compra = Compra::create([
                'user_id'             => Auth::id(),
                'proveedore_id'       => $request->proveedore_id ?: null,
                'comprobante_id'      => $request->comprobante_id ?: null,
                'numero_comprobante'  => $request->numero_comprobante ?: null,
                'comprobante_path'    => $comprobantePath,
                'metodo_pago'         => $request->metodo_pago ?: null,
                'fecha_hora'          => $request->fecha_hora ?: null,
                'impuesto'            => 0,
                'subtotal'            => $request->subtotal,
                'total'               => $request->total,
            ]);

            //Llenar tabla compra_producto
            //1.Recuperar los arrays
            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayPrecioCompra = $request->get('arraypreciocompra');
            $arrayFechaVencimiento = $request->get('arrayfechavencimiento');
            //2.Realizar el llenado

            $siseArray = count($arrayProducto_id);
            $cont = 0;
            while ($cont < $siseArray) {
                $fechaVenc = !empty($arrayFechaVencimiento[$cont]) ? $arrayFechaVencimiento[$cont] : null;

                $compra->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                        'id' => Str::uuid()->toString(),
                        'cantidad' => $arrayCantidad[$cont],
                        'precio_compra' => $arrayPrecioCompra[$cont],
                        'fecha_vencimiento' => $fechaVenc,
                    ]
                ]);

                //3.Despachar evento de Creacion de registro
                CreateCompraDetalleEvent::dispatch(
                    $compra,
                    $arrayProducto_id[$cont],
                    $arrayCantidad[$cont],
                    $arrayPrecioCompra[$cont],
                    $fechaVenc
                );

                $cont++;
            }

            DB::commit();
            ActivityLogService::log('Creación de compra', 'Compras', ['compra_id' => $compra->id, 'total' => $compra->total]);
            return redirect()->route('compras.index')->with('success', 'compra exitosa');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear la compra', ['error' => $e->getMessage()]);
            return redirect()->route('compras.index')->with('error', 'Ups, algo falló');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Compra $compra): View
    {
        $empresa = $this->empresaService->obtenerEmpresa();
        return view('compra.show', compact('compra', 'empresa'));
    }

    /**
     * Extract invoice fields from an image/PDF using Gemini Vision (same pattern as ProductoController).
     */
    public function extractFromFile(Request $request): JsonResponse
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'IA no configurada (GEMINI_API_KEY faltante).'], 503);
        }

        $key = 'extract_factura:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return response()->json(['error' => 'Demasiadas solicitudes. Intenta más tarde.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $request->validate([
            'image_base64' => 'required|string',
            'image_mime'   => 'nullable|string',
        ]);

        $b64  = $request->input('image_base64');
        $mime = $request->input('image_mime', 'image/jpeg') ?: 'image/jpeg';

        $metodosValidos = 'EFECTIVO, TARJETA, NEQUI, DAVIPLATA, FIADO, VENTA_DIGITAL';

        $prompt = "Analiza esta imagen de una factura, remisión o comprobante de compra.\n"
            . "Responde ÚNICAMENTE con un JSON válido (sin markdown, sin bloques de código) con esta estructura:\n"
            . "{\n"
            . "  \"numero_comprobante\": \"número o código de la factura/comprobante\",\n"
            . "  \"fecha\": \"fecha en formato YYYY-MM-DDTHH:mm (si no hay hora usa T00:00)\",\n"
            . "  \"metodo_pago\": \"uno de: {$metodosValidos}\",\n"
            . "  \"proveedor_nombre\": \"nombre o razón social del proveedor/empresa emisora\"\n"
            . "}\n"
            . "Si no puedes extraer un campo con certeza, usa null. Solo el JSON.";

        try {
            $response = Http::timeout(25)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'contents' => [['parts' => [
                            ['inline_data' => ['mime_type' => $mime, 'data' => $b64]],
                            ['text' => $prompt],
                        ]]],
                        'generationConfig' => [
                            'temperature'     => 0.1,
                            'maxOutputTokens' => 300,
                        ],
                    ]
                );

            if ($response->failed()) {
                return response()->json(['error' => 'Error IA: HTTP ' . $response->status()], 500);
            }

            $data = $response->json();
            $raw  = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $raw  = preg_replace('/```(?:json)?\s*|\s*```/', '', trim($raw));
            $extracted = json_decode($raw, true) ?? [];

            // Intentar encontrar proveedor por razón social
            $proveedorId = null;
            if (!empty($extracted['proveedor_nombre'])) {
                $needle = $extracted['proveedor_nombre'];
                $proveedor = Proveedore::whereHas('persona', function ($q) use ($needle) {
                    $q->where('razon_social', 'like', '%' . $needle . '%');
                })->first();
                if ($proveedor) $proveedorId = $proveedor->id;
            }

            return response()->json([
                'numero_comprobante' => $extracted['numero_comprobante'] ?? null,
                'fecha_hora'         => $extracted['fecha'] ?? null,
                'metodo_pago'        => $extracted['metodo_pago'] ?? null,
                'proveedor_nombre'   => $extracted['proveedor_nombre'] ?? null,
                'proveedore_id'      => $proveedorId,
            ]);

        } catch (\Exception $e) {
            Log::error('extractFromFile error', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /*
        Compra::where('id', $id)
            ->update([
                'estado' => 0
            ]);

        return redirect()->route('compras.index')->with('success', 'Compra eliminada');*/
    }
}
