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
        $producto = Producto::create([
            'codigo' => $data['codigo'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'img_path' => isset($data['img_path']) && $data['img_path']
                ? $this->handleUploadImage($data['img_path'])
                : null,
            'marca_id' => $data['marca_id'],
            'categoria_id' => $data['categoria_id'],
            'presentacione_id' => $data['presentacione_id'],
        ]);

        return $producto;
    }

    /**
     * Editar un registro
     */
    public function editarProducto(array $data, Producto $producto): Producto
    {

        $producto->update([
            'codigo' => $data['codigo'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'img_path' => isset($data['img_path']) && $data['img_path']
                ? $this->handleUploadImage($data['img_path'], $producto->img_path)
                : $producto->img_path,
            'marca_id' => $data['marca_id'],
            'categoria_id' => $data['categoria_id'],
            'presentacione_id' => $data['presentacione_id'],
        ]);

        return $producto;
    }


    /**
     * Guarda una imagen en el Storage
     * 
     */
    private function handleUploadImage(UploadedFile $image, $img_path = null): string
    {
        // FORCE Cloudinary if properly configured (Vercel Fix)
        // We use config() because env() returns null if config is cached
        $isCloudinaryConfigured = config('filesystems.disks.cloudinary.cloud_name');
        $defaultDisk = config('filesystems.default');
        
        $disk = $isCloudinaryConfigured ? 'cloudinary' : $defaultDisk;

        // DEBUG: Capture all possible env sources
        $envVar = $_ENV['CLOUDINARY_URL'] ?? 'null';
        $serverVar = $_SERVER['CLOUDINARY_URL'] ?? 'null';
        $getenvVar = getenv('CLOUDINARY_URL') !== false ? 'Present' : 'null';

        if ($disk !== 'cloudinary' && (env('APP_ENV') === 'production' || env('VERCEL'))) {
             throw new \Exception("CRITICAL ERROR: Attempting to '{$disk}'. " . 
                "Configured: " . ($isCloudinaryConfigured ? 'YES' : 'NO') . ". " . 
                "SOURCES -> ENV: {$envVar}, SERVER: {$serverVar}, getenv: {$getenvVar}. " . 
                "Please verify 'CLOUDINARY_URL' in Vercel Settings.");
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
