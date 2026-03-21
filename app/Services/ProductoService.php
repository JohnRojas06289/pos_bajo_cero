<?php

namespace App\Services;

use App\Models\Producto;
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
