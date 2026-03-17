<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Caracteristica;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Producto;
use App\Services\ActivityLogService;
use App\Services\ProductoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class ProductoController extends Controller
{
    protected $productoService;

    function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|eliminar-producto', ['only' => ['index']]);
        $this->middleware('permission:crear-producto', ['only' => ['create', 'store', 'import']]);
        $this->middleware('permission:editar-producto', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-producto', ['only' => ['destroy']]);
        $this->middleware('permission:ver-producto', ['only' => ['export']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $productos = Producto::with([
            'categoria.caracteristica',
            'marca.caracteristica',
            'presentacione.caracteristica'
        ])
            ->orderBy('nombre')
            ->get();

        return view('producto.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $marcas = Cache::remember('marcas_activas', 3600, fn () =>
            Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
                ->select('marcas.id as id', 'c.nombre as nombre')
                ->where('c.estado', 1)
                ->orderBy('c.nombre')
                ->get()
        );

        $presentaciones = Cache::remember('presentaciones_activas', 3600, fn () =>
            Presentacione::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
                ->select('presentaciones.id as id', 'c.nombre as nombre')
                ->where('c.estado', 1)
                ->orderBy('c.nombre')
                ->get()
        );

        $categorias = Cache::remember('categorias_activas_form', 3600, fn () =>
            Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
                ->select('categorias.id as id', 'c.nombre as nombre')
                ->where('c.estado', 1)
                ->orderBy('c.nombre')
                ->get()
        );

        // Get the last product code and suggest the next one (numeric sorting)
        $ultimoProducto = Producto::orderByRaw('LENGTH(codigo) DESC, codigo DESC')->first();
        $codigoSugerido = $ultimoProducto && $ultimoProducto->codigo ? (string)((int)$ultimoProducto->codigo + 1) : '1';

        return view('producto.create', compact('marcas', 'presentaciones', 'categorias', 'codigoSugerido'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request): RedirectResponse
    {
        // BATCH MODE: crear múltiples productos por talla
        if ($request->has('tallas_ids') && is_array($request->input('tallas_ids')) && count($request->input('tallas_ids')) > 0) {
            return $this->storeBatch($request);
        }

        try {
            $data = $request->validated();

            // Auto-generate code if empty
            if (empty($data['codigo'])) {
                $lastCodigo = Producto::orderByRaw('LENGTH(codigo) DESC, codigo DESC')->first();
                $data['codigo'] = ($lastCodigo && $lastCodigo->codigo) ? (string)((int)$lastCodigo->codigo + 1) : '1';
            }

            // MANUAL UNIQUE CHECK (Vercel SQLite Compatibility)
            if (Producto::where('nombre', $data['nombre'])->exists()) {
                return redirect()->back()->withErrors(['nombre' => 'El nombre del producto ya existe.'])->withInput();
            }
            if (!empty($data['codigo']) && Producto::where('codigo', $data['codigo'])->exists()) {
                return redirect()->back()->withErrors(['codigo' => 'El código del producto ya existe.'])->withInput();
            }

            $this->productoService->crearProducto($data);
            ActivityLogService::log('Creación de producto', 'Productos', $data);
            return redirect()->route('productos.index')->with('success', 'Producto registrado');
        } catch (Throwable $e) {
            Log::error('Error al crear el producto', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Crear múltiples productos (uno por talla) en modo batch.
     */
    private function storeBatch(Request $request): RedirectResponse
    {
        try {
            $baseNombre = $request->input('nombre');
            if (empty($baseNombre)) {
                return redirect()->back()->withErrors(['nombre' => 'El nombre es requerido.'])->withInput();
            }

            $tallasIds = $request->input('tallas_ids', []);
            $created = 0;
            $skipped = [];

            foreach ($tallasIds as $presentacione_id) {
                $presentacion = Presentacione::with('caracteristica')->find($presentacione_id);
                $tallaNombre = $presentacion
                    ? ($presentacion->caracteristica->nombre ?? $presentacion->sigla)
                    : null;
                $nombre = $tallaNombre ? $baseNombre . ' - ' . $tallaNombre : $baseNombre;

                if (Producto::where('nombre', $nombre)->exists()) {
                    $skipped[] = $nombre;
                    continue;
                }

                // Obtener el siguiente código disponible
                $lastCodigo = Producto::orderByRaw('LENGTH(codigo) DESC, codigo DESC')->first();
                $codigo = ($lastCodigo && $lastCodigo->codigo) ? (string)((int)$lastCodigo->codigo + 1) : '1';

                Producto::create([
                    'codigo'           => $codigo,
                    'nombre'           => $nombre,
                    'descripcion'      => $request->input('descripcion'),
                    'img_path'         => null, // imágenes se agregan individualmente por edición
                    'marca_id'         => $request->input('marca_id') ?: null,
                    'categoria_id'     => $request->input('categoria_id') ?: null,
                    'presentacione_id' => $presentacione_id,
                    'color'            => $request->input('color'),
                    'material'         => $request->input('material'),
                    'genero'           => $request->input('genero'),
                    'precio'           => $request->input('precio') ?: null,
                    'estado'           => 0,
                ]);
                $created++;
            }

            ActivityLogService::log('Creación batch de productos', 'Productos', [
                'base_nombre' => $baseNombre,
                'creados'     => $created,
                'saltados'    => count($skipped),
            ]);

            $msg = $created . ' producto(s) creado(s) con tallas.';
            if (!empty($skipped)) {
                $msg .= ' Saltados (ya existen): ' . implode(', ', $skipped);
            }
            return redirect()->route('productos.index')->with('success', $msg);
        } catch (Throwable $e) {
            Log::error('Error en creación batch de productos', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['nombre' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto): View
    {
        $marcas        = Cache::remember('marcas_activas', 3600, fn () =>
            Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
                ->select('marcas.id as id', 'c.nombre as nombre')
                ->where('c.estado', 1)->orderBy('c.nombre')->get()
        );
        $presentaciones = Cache::remember('presentaciones_activas', 3600, fn () =>
            Presentacione::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
                ->select('presentaciones.id as id', 'c.nombre as nombre')
                ->where('c.estado', 1)->orderBy('c.nombre')->get()
        );
        $categorias = Cache::remember('categorias_activas_form', 3600, fn () =>
            Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
                ->select('categorias.id as id', 'c.nombre as nombre')
                ->where('c.estado', 1)->orderBy('c.nombre')->get()
        );

        return view('producto.edit', compact('producto', 'marcas', 'presentaciones', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): RedirectResponse
    {
        try {
            $data = $request->validated();

             // MANUAL VALIDATION (Vercel SQLite Compatibility) - Check Unique
            if (Producto::where('nombre', $data['nombre'])->where('id', '!=', $producto->id)->exists()) {
                return redirect()->back()->withErrors(['nombre' => 'El nombre ya existe.'])->withInput();
            }
            if (!empty($data['codigo']) && Producto::where('codigo', $data['codigo'])->where('id', '!=', $producto->id)->exists()) {
                 return redirect()->back()->withErrors(['codigo' => 'El código ya existe.'])->withInput();
            }

            // Verify Foreign Keys (Manual 'exists')
            if (!empty($data['marca_id']) && !\App\Models\Marca::where('id', $data['marca_id'])->exists()) {
                 return redirect()->back()->withErrors(['marca_id' => 'La marca seleccionada no existe.'])->withInput();
            }
             if (!empty($data['categoria_id']) && !\App\Models\Categoria::where('id', $data['categoria_id'])->exists()) {
                 return redirect()->back()->withErrors(['categoria_id' => 'La categoría seleccionada no existe.'])->withInput();
            }
             if (!empty($data['presentacione_id']) && !\App\Models\Presentacione::where('id', $data['presentacione_id'])->exists()) {
                 return redirect()->back()->withErrors(['presentacione_id' => 'La presentación seleccionada no existe.'])->withInput();
            }
            
            \Illuminate\Support\Facades\Log::info('Producto update request', [
                'has_file' => $request->hasFile('img_path'),
                'file_valid' => $request->hasFile('img_path') ? $request->file('img_path')->isValid() : false,
                'file_error' => $request->hasFile('img_path') ? $request->file('img_path')->getError() : 'N/A',
                'file_size' => $request->hasFile('img_path') ? $request->file('img_path')->getSize() : 'N/A',
                'post_data' => $request->except(['img_path']),
            ]);

            $this->productoService->editarProducto($request->validated(), $producto);
            ActivityLogService::log('Edición de producto', 'Productos', $request->validated());
            return redirect()->route('productos.index')->with('success', 'Producto editado');
        } catch (Throwable $e) {
            Log::error('Error al editar el producto', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Export products to CSV/Excel file
     */
    public function export()
    {
        try {
            // Get all products with relationships
            $productos = Producto::with([
                'categoria.caracteristica',
                'marca.caracteristica',
                'presentacione.caracteristica',
                'inventario'
            ])->get();

            // Create filename with current date
            $filename = 'productos_' . date('Y-m-d') . '.csv';

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            // Create callback for streaming CSV
            $callback = function() use ($productos) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8 (helps Excel recognize special characters)
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Add CSV headers
                fputcsv($file, [
                    'Código',
                    'Nombre',
                    'Descripción',
                    'Precio',
                    'Categoría',
                    'Marca',
                    'Presentación',
                    'Stock',
                    'Estado'
                ]);

                // Add product data
                foreach ($productos as $producto) {
                    fputcsv($file, [
                        $producto->codigo,
                        $producto->nombre,
                        $producto->descripcion ?? 'Sin descripción',
                        number_format($producto->precio ?? 0, 2, '.', ''),
                        $producto->categoria->caracteristica->nombre ?? 'Sin categoría',
                        $producto->marca->caracteristica->nombre ?? 'Sin marca',
                        $producto->presentacione->caracteristica->nombre ?? 'Sin presentación',
                        $producto->inventario->stock ?? 0,
                        $producto->estado ? 'Activo' : 'Inactivo'
                    ]);
                }

                fclose($file);
            };

            ActivityLogService::log('Exportación de productos', 'Productos', ['total' => $productos->count()]);
            
            return response()->stream($callback, 200, $headers);

        } catch (Throwable $e) {
            Log::error('Error al exportar productos', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for product import
     */
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Productos');

            $columns = ['Código', 'Nombre', 'Descripción', 'Precio', 'Categoría', 'Marca', 'Presentación', 'Stock', 'Estado', 'Color', 'Material', 'Género', 'URL_Imagen'];
            foreach ($columns as $i => $col) {
                $sheet->setCellValueByColumnAndRow($i + 1, 1, $col);
            }

            $sample = ['001', 'Chaqueta de Cuero Negra', 'Chaqueta elegante de cuero genuino premium', '150000', 'Chaquetas', 'Sin marca', 'M', '10', 'Activo', 'Negro', 'Cuero', 'Unisex', ''];
            foreach ($sample as $i => $val) {
                $sheet->setCellValueByColumnAndRow($i + 1, 2, $val);
            }

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            $sheet->freezePane('A2');

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="plantilla_productos.xlsx"',
                'Cache-Control' => 'no-cache',
            ];

            $callback = function () use ($spreadsheet) {
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            };

            ActivityLogService::log('Descarga plantilla Excel productos', 'Productos', []);
            return response()->stream($callback, 200, $headers);
        } catch (Throwable $e) {
            Log::error('Error generando plantilla Excel', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Error al generar plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Generate AI description for a product (single or from form data)
     */
    public function generateDescription(Request $request): JsonResponse
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'IA no configurada (GEMINI_API_KEY faltante).'], 503);
        }

        $userId = auth()->id();
        $key = "gen_desc_{$userId}";
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['error' => 'Demasiadas solicitudes. Intenta en unos minutos.'], 429);
        }
        RateLimiter::hit($key, 3600);

        // Load from product ID or use provided form data
        if ($request->filled('producto_id')) {
            $producto = Producto::with(['categoria.caracteristica', 'marca.caracteristica'])->find($request->input('producto_id'));
            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado.'], 404);
            }
            $nombre   = $producto->nombre;
            $categoria = $producto->categoria->caracteristica->nombre ?? null;
            $marca     = $producto->marca->caracteristica->nombre ?? null;
            $color     = $producto->color;
            $material  = $producto->material;
            $genero    = ($producto->genero && $producto->genero !== 'Unisex') ? $producto->genero : null;
        } else {
            $nombre    = trim($request->input('nombre', ''));
            $categoria = $request->input('categoria');
            $marca     = $request->input('marca');
            $color     = $request->input('color');
            $material  = $request->input('material');
            $genero    = ($request->input('genero') && $request->input('genero') !== 'Unisex') ? $request->input('genero') : null;
        }

        if (empty($nombre)) {
            return response()->json(['error' => 'El nombre del producto es requerido.'], 422);
        }

        $prompt = "Genera una descripción de producto atractiva y concisa para una tienda de ropa en Colombia. "
            . "Producto: '{$nombre}'."
            . ($categoria ? " Categoría: {$categoria}." : "")
            . ($marca ? " Marca: {$marca}." : "")
            . ($color ? " Color: {$color}." : "")
            . ($material ? " Material: {$material}." : "")
            . ($genero ? " Para: {$genero}." : "")
            . " Escribe exactamente 2-3 oraciones, sin título, sin emojis, en español colombiano. "
            . "Resalta beneficios, estilo y calidad.";

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.8, 'maxOutputTokens' => 150],
                    ]
                );

            if ($response->failed()) {
                return response()->json(['error' => 'Error al contactar la IA (' . $response->status() . ').'], 500);
            }

            $text = trim($response->json('candidates.0.content.parts.0.text') ?? '');
            if (empty($text)) {
                return response()->json(['error' => 'La IA no generó respuesta.'], 500);
            }

            return response()->json(['description' => $text]);
        } catch (Throwable $e) {
            Log::error('Error generando descripción con IA', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate AI descriptions for all products without description (batch)
     */
    public function generateAllDescriptions(): JsonResponse
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'IA no configurada (GEMINI_API_KEY faltante).'], 503);
        }

        $userId = auth()->id();
        $key = "gen_desc_batch_{$userId}";
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json(['error' => 'Máximo 3 generaciones masivas por hora.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $productos = Producto::with(['categoria.caracteristica', 'marca.caracteristica'])
            ->where(function ($q) {
                $q->whereNull('descripcion')
                  ->orWhere('descripcion', '')
                  ->orWhere('descripcion', 'Sin descripción');
            })
            ->limit(15)
            ->get();

        if ($productos->isEmpty()) {
            return response()->json(['message' => 'Todos los productos ya tienen descripción.', 'count' => 0]);
        }

        $updated = 0;
        $errors  = [];

        foreach ($productos as $producto) {
            try {
                $prompt = "Genera una descripción de producto atractiva para una tienda de ropa colombiana. "
                    . "Producto: '{$producto->nombre}'."
                    . ($producto->categoria ? " Categoría: " . ($producto->categoria->caracteristica->nombre ?? '') . "." : "")
                    . ($producto->marca ? " Marca: " . ($producto->marca->caracteristica->nombre ?? '') . "." : "")
                    . ($producto->color ? " Color: {$producto->color}." : "")
                    . ($producto->material ? " Material: {$producto->material}." : "")
                    . " Escribe 2-3 oraciones, sin emojis, en español colombiano.";

                $response = Http::timeout(12)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                        [
                            'contents' => [['parts' => [['text' => $prompt]]]],
                            'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 150],
                        ]
                    );

                if ($response->successful()) {
                    $text = trim($response->json('candidates.0.content.parts.0.text') ?? '');
                    if (!empty($text)) {
                        $producto->update(['descripcion' => $text]);
                        $updated++;
                    }
                }
                usleep(250000); // 250ms delay
            } catch (\Exception $e) {
                $errors[] = $producto->nombre;
            }
        }

        $remaining = Producto::where(function ($q) {
            $q->whereNull('descripcion')->orWhere('descripcion', '');
        })->count();

        ActivityLogService::log('Generación masiva de descripciones IA', 'Productos', ['actualizados' => $updated]);

        return response()->json([
            'message'   => "{$updated} descripciones generadas exitosamente.",
            'count'     => $updated,
            'remaining' => $remaining,
            'errors'    => count($errors) > 0 ? implode(', ', array_slice($errors, 0, 3)) : null,
        ]);
    }

    /**
     * Import products from CSV file
     */
    public function import(Request $request): RedirectResponse
    {
        try {
            // Validate file upload
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120'
            ]);

            $file      = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            // Route to Excel importer for .xlsx/.xls
            if (in_array($extension, ['xlsx', 'xls'])) {
                return $this->importFromExcel($file);
            }

            $path = $file->getRealPath();

            // Open and read CSV
            $csv = fopen($path, 'r');
            
            // Skip BOM if present
            $bom = fread($csv, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($csv);
            }
            
            // Read header
            $header = fgetcsv($csv);
            
            if (!$header) {
                return redirect()->route('productos.index')->with('error', 'El archivo CSV está vacío o no tiene el formato correcto.');
            }

            $created = 0;
            $failed = 0;
            $errors = [];

            // Process each row
            while (($row = fgetcsv($csv)) !== false) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map CSV columns to data
                    $data = array_combine($header, $row);
                    
                    // Validate required fields
                    if (empty($data['Nombre'])) {
                        throw new \Exception('Nombre es requerido');
                    }

                    // Find or create relationships
                    $categoria = null;
                    if (!empty($data['Categoría']) && $data['Categoría'] !== 'Sin categoría') {
                        $categoriaCaract = Caracteristica::where('nombre', $data['Categoría'])->first();
                        if ($categoriaCaract) {
                            $categoria = Categoria::where('caracteristica_id', $categoriaCaract->id)->first();
                        }
                    }

                    $marca = null;
                    if (!empty($data['Marca']) && $data['Marca'] !== 'Sin marca') {
                        $marcaCaract = Caracteristica::where('nombre', $data['Marca'])->first();
                        if ($marcaCaract) {
                            $marca = Marca::where('caracteristica_id', $marcaCaract->id)->first();
                        }
                    }

                    $presentacion = null;
                    if (!empty($data['Presentación']) && $data['Presentación'] !== 'Sin presentación') {
                        $presentacionCaract = Caracteristica::where('nombre', $data['Presentación'])->first();
                        if ($presentacionCaract) {
                            $presentacion = Presentacione::where('caracteristica_id', $presentacionCaract->id)->first();
                        }
                    }

                    // Create product
                    $producto = Producto::create([
                        'codigo' => !empty($data['Código']) ? $data['Código'] : null,
                        'nombre' => $data['Nombre'],
                        'descripcion' => $data['Descripción'] ?? null,
                        'precio' => !empty($data['Precio']) ? (float)$data['Precio'] : 0,
                        'categoria_id' => $categoria?->id,
                        'marca_id' => $marca?->id,
                        'presentacione_id' => $presentacion?->id,
                        'estado' => (isset($data['Estado']) && $data['Estado'] === 'Activo') ? 1 : 0
                    ]);

                    // Create inventory record if stock is provided
                    if (isset($data['Stock']) && is_numeric($data['Stock'])) {
                        $producto->inventario()->create([
                            'stock' => (int)$data['Stock']
                        ]);
                    }

                    $created++;

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Fila " . ($created + $failed + 1) . ": " . $e->getMessage();
                }
            }

            fclose($csv);

            ActivityLogService::log('Importación de productos', 'Productos', [
                'creados' => $created,
                'fallidos' => $failed
            ]);

            $message = "Importación completada: {$created} productos creados";
            if ($failed > 0) {
                $message .= ", {$failed} fallidos. Errores: " . implode('; ', array_slice($errors, 0, 5));
            }

            return redirect()->route('productos.index')->with('success', $message);

        } catch (Throwable $e) {
            Log::error('Error al importar productos', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Import products from an Excel (.xlsx/.xls) file
     */
    private function importFromExcel($file): RedirectResponse
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);

            if (empty($rows) || count($rows) < 2) {
                return redirect()->route('productos.index')->with('error', 'El archivo Excel está vacío o solo tiene encabezados.');
            }

            $header  = array_map('trim', $rows[0]);
            $created = 0;
            $failed  = 0;
            $errors  = [];

            for ($i = 1; $i < count($rows); $i++) {
                try {
                    $row = $rows[$i];
                    if (empty(array_filter($row))) continue;

                    $data = array_combine($header, array_pad($row, count($header), null));
                    $data = array_map(fn($v) => is_string($v) ? trim($v) : $v, $data);

                    if (empty($data['Nombre'])) continue;

                    if (Producto::where('nombre', $data['Nombre'])->exists()) {
                        $failed++;
                        $errors[] = "Fila " . ($i + 1) . ": '{$data['Nombre']}' ya existe.";
                        continue;
                    }

                    $categoria = null;
                    if (!empty($data['Categoría']) && $data['Categoría'] !== 'Sin categoría') {
                        $categoriaCaract = Caracteristica::where('nombre', $data['Categoría'])->first();
                        if ($categoriaCaract) {
                            $categoria = Categoria::where('caracteristica_id', $categoriaCaract->id)->first();
                        }
                    }

                    $marca = null;
                    if (!empty($data['Marca']) && $data['Marca'] !== 'Sin marca') {
                        $marcaCaract = Caracteristica::where('nombre', $data['Marca'])->first();
                        if ($marcaCaract) {
                            $marca = Marca::where('caracteristica_id', $marcaCaract->id)->first();
                        }
                    }

                    $presentacion = null;
                    if (!empty($data['Presentación']) && $data['Presentación'] !== 'Sin presentación') {
                        $presentacionCaract = Caracteristica::where('nombre', $data['Presentación'])->first();
                        if ($presentacionCaract) {
                            $presentacion = Presentacione::where('caracteristica_id', $presentacionCaract->id)->first();
                        }
                    }

                    $codigo = !empty($data['Código']) ? $data['Código'] : null;
                    if ($codigo && Producto::where('codigo', $codigo)->exists()) {
                        $codigo = null;
                    }
                    if (!$codigo) {
                        $last   = Producto::orderByRaw('LENGTH(codigo) DESC, codigo DESC')->first();
                        $codigo = ($last && $last->codigo) ? (string)((int)$last->codigo + 1) : '1';
                    }

                    $producto = Producto::create([
                        'codigo'           => $codigo,
                        'nombre'           => $data['Nombre'],
                        'descripcion'      => $data['Descripción'] ?? null,
                        'precio'           => !empty($data['Precio']) ? (float)$data['Precio'] : 0,
                        'categoria_id'     => $categoria?->id,
                        'marca_id'         => $marca?->id,
                        'presentacione_id' => $presentacion?->id,
                        'estado'           => (strtolower($data['Estado'] ?? '') === 'activo') ? 1 : 0,
                        'color'            => $data['Color'] ?? null,
                        'material'         => $data['Material'] ?? null,
                        'genero'           => in_array($data['Género'] ?? '', ['Hombre', 'Mujer', 'Unisex']) ? $data['Género'] : 'Unisex',
                    ]);

                    if (isset($data['Stock']) && is_numeric($data['Stock'])) {
                        $producto->inventario()->create(['stock' => (int)$data['Stock']]);
                    }

                    if (!empty($data['URL_Imagen'])) {
                        $producto->update(['img_path' => $data['URL_Imagen']]);
                    }

                    $created++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Fila " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            ActivityLogService::log('Importación Excel de productos', 'Productos', ['creados' => $created, 'fallidos' => $failed]);

            $message = "Importación completada: {$created} productos creados.";
            if ($failed > 0) {
                $message .= " {$failed} omitidos. " . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()->route('productos.index')->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Error importando Excel', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Error al importar Excel: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /*
        $message = '';
        $producto = Producto::find($id);
        if ($producto->estado == 1) {
            Producto::where('id', $producto->id)
                ->update([
                    'estado' => 0
                ]);
            $message = 'Producto eliminado';
        } else {
            Producto::where('id', $producto->id)
                ->update([
                    'estado' => 1
                ]);
            $message = 'Producto restaurado';
        }

        return redirect()->route('productos.index')->with('success', $message);*/
    }
}
