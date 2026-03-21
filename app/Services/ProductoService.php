<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Variante;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductoService
{
    /**
     * Crear un Registro
     */
    public function crearProducto(array $data): Producto
    {
        // Main image
        $mainPath = null;
        $extraFiles = $data['imagenes_files'] ?? [];

        // If no dedicated img_path but there are extra files, use first as main
        if (isset($data['img_path']) && $data['img_path']) {
            $mainPath = $this->handleUploadImage($data['img_path']);
        } elseif (!empty($extraFiles)) {
            $first = array_shift($extraFiles);
            if ($first) $mainPath = $this->handleUploadImage($first);
        }

        // Additional images
        $imagenesPaths = [];
        foreach ($extraFiles as $file) {
            if ($file && method_exists($file, 'isValid') && $file->isValid()) {
                $imagenesPaths[] = $this->handleUploadImage($file);
            }
        }

        $producto = Producto::create([
            'codigo'           => $data['codigo'] ?? null,
            'nombre'           => $data['nombre'],
            'descripcion'      => $data['descripcion'] ?? null,
            'img_path'         => $mainPath,
            'imagenes'         => !empty($imagenesPaths) ? $imagenesPaths : null,
            'marca_id'         => $data['marca_id'] ?? null,
            'categoria_id'     => $data['categoria_id'] ?? null,
            'presentacione_id' => $data['presentacione_id'] ?? null,
            'color'            => $data['color'] ?? null,
            'material'         => $data['material'] ?? null,
            'genero'           => $data['genero'] ?? 'Unisex',
            'precio'           => $data['precio'] ?? null,
            'estado'           => 0,
        ]);

        return $producto;
    }

    /**
     * Editar un registro
     */
    public function editarProducto(array $data, Producto $producto): Producto
    {
        // Main image: only replace if new one provided
        $mainPath = $producto->img_path;
        if (isset($data['img_path']) && $data['img_path']) {
            $mainPath = $this->handleUploadImage($data['img_path'], $producto->img_path);
        }

        // Additional new images (appended to existing)
        $imagenes = $producto->imagenes ?? [];
        foreach ($data['imagenes_nuevas_files'] ?? [] as $file) {
            if ($file && method_exists($file, 'isValid') && $file->isValid()) {
                $imagenes[] = $this->handleUploadImage($file);
            }
        }

        $producto->update([
            'codigo'           => $data['codigo'],
            'nombre'           => $data['nombre'],
            'descripcion'      => $data['descripcion'] ?? null,
            'img_path'         => $mainPath,
            'imagenes'         => !empty($imagenes) ? array_values($imagenes) : null,
            'marca_id'         => $data['marca_id'] ?? null,
            'categoria_id'     => $data['categoria_id'] ?? null,
            'presentacione_id' => $data['presentacione_id'] ?? null,
            'color'            => $data['color'] ?? null,
            'material'         => $data['material'] ?? null,
            'genero'           => $data['genero'] ?? 'Unisex',
            'precio'           => $data['precio'] ?? null,
            'estado'           => $data['estado'] ?? $producto->estado,
        ]);

        return $producto;
    }


    /**
     * Crear una variante para un producto.
     *
     * $data puede contener:
     *   presentacione_id, color, codigo, stock, img_path (UploadedFile o string)
     */
    public function crearVariante(array $data, Producto $producto): Variante
    {
        $imgPath = null;
        if (!empty($data['img_path']) && $data['img_path'] instanceof UploadedFile) {
            $imgPath = $this->handleUploadImage($data['img_path']);
        }

        return Variante::create([
            'producto_id'      => $producto->id,
            'presentacione_id' => $data['presentacione_id'] ?: null,
            'color'            => $data['color'] ?: null,
            'codigo'           => $data['codigo'] ?: null,
            'stock'            => (int) ($data['stock'] ?? 0),
            'img_path'         => $imgPath,
        ]);
    }

    /**
     * Sincronizar variantes de un producto al editar.
     *
     * - Si la variante tiene 'id' → actualizar
     * - Si no tiene 'id' → crear nueva
     * - Variantes existentes que no estén en $variantesData → eliminar
     *
     * $variantesData = array de arrays con keys:
     *   id?, presentacione_id, color, codigo, stock, img_path?
     * $files = array de UploadedFile indexado igual que $variantesData
     */
    public function actualizarVariantes(array $variantesData, Producto $producto, array $files = []): void
    {
        $idsEnviados = [];

        foreach ($variantesData as $index => $vData) {
            $file = $files[$index] ?? null;

            if (!empty($vData['id'])) {
                // Actualizar variante existente
                $variante = Variante::where('id', $vData['id'])
                    ->where('producto_id', $producto->id)
                    ->first();

                if (!$variante) continue;

                $imgPath = $variante->img_path;
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $imgPath = $this->handleUploadImage($file, $variante->img_path);
                }

                $variante->update([
                    'presentacione_id' => $vData['presentacione_id'] ?: null,
                    'color'            => $vData['color'] ?: null,
                    'codigo'           => $vData['codigo'] ?: null,
                    'stock'            => (int) ($vData['stock'] ?? 0),
                    'img_path'         => $imgPath,
                ]);

                $idsEnviados[] = $variante->id;
            } else {
                // Nueva variante
                $imgPath = null;
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $imgPath = $this->handleUploadImage($file);
                }

                $nueva = Variante::create([
                    'producto_id'      => $producto->id,
                    'presentacione_id' => $vData['presentacione_id'] ?: null,
                    'color'            => $vData['color'] ?: null,
                    'codigo'           => $vData['codigo'] ?: null,
                    'stock'            => (int) ($vData['stock'] ?? 0),
                    'img_path'         => $imgPath,
                ]);

                $idsEnviados[] = $nueva->id;
            }
        }

        // Eliminar variantes que ya no están en el formulario
        if (!empty($idsEnviados)) {
            Variante::where('producto_id', $producto->id)
                ->whereNotIn('id', $idsEnviados)
                ->delete();
        }

        // Garantizar al menos una variante
        if (Variante::where('producto_id', $producto->id)->count() === 0) {
            Variante::create([
                'producto_id' => $producto->id,
                'stock'       => 0,
            ]);
        }
    }

    /**
     * Guarda una imagen en el Storage
     * 
     */
    public function handleUploadImage(UploadedFile $image, $img_path = null): string
    {
        // FORCE Cloudinary if properly configured (Vercel Fix)
        // We use config() because env() returns null if config is cached
        $isCloudinaryConfigured = config('filesystems.disks.cloudinary.cloud_name');
        $defaultDisk = config('filesystems.default');
        
        $disk = $isCloudinaryConfigured ? 'cloudinary' : $defaultDisk;

        // Capture all possible env sources
        $envVar = $_ENV['CLOUDINARY_URL'] ?? 'null';
        $serverVar = $_SERVER['CLOUDINARY_URL'] ?? 'null';
        $getenvVar = getenv('CLOUDINARY_URL') !== false ? 'Present' : 'null';

        if ($disk !== 'cloudinary' && (env('APP_ENV') === 'production' || env('VERCEL'))) {
             // More graceful error handling: Log the issue and throw a validation error
             \Illuminate\Support\Facades\Log::critical("CLOUDINARY MISSING. Env: {$envVar}, Server: {$serverVar}, getenv: {$getenvVar}");
             
             throw \Illuminate\Validation\ValidationException::withMessages([
                 'img_path' => 'Error de configuración del servidor: La variable CLOUDINARY_URL no está definida en Vercel.'
             ]);
        }

        if ($img_path) {
            // Check if file exists before deleting
            if (Storage::disk($disk)->exists($img_path)) {
                Storage::disk($disk)->delete($img_path);
            }
        }

        $name = uniqid() . '.' . $image->getClientOriginalExtension();
        
        // Store in 'productos' folder
        $path = $image->storeAs('productos', $name, $disk);

        if (!$path) {
             throw new \Exception('Error al subir la imagen al disco: ' . $disk);
        }

        return $path;
    }
}
