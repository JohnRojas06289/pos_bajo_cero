<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'familia_id',
        'codigo',
        'nombre',
        'descripcion',
        'img_path',
        'imagenes',
        'estado',
        'precio',
        'marca_id',
        'presentacione_id',
        'categoria_id',
        'color',
        'material',
        'genero'
    ];

    protected $casts = [
        'imagenes' => 'array',
    ];

    public function compras(): BelongsToMany
    {
        return $this->belongsToMany(Compra::class)
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'fecha_vencimiento');
    }

    public function ventas(): BelongsToMany
    {
        return $this->belongsToMany(Venta::class)
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_venta');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    public function presentacione(): BelongsTo
    {
        return $this->belongsTo(Presentacione::class);
    }

    public function inventario(): HasOne
    {
        return $this->hasOne(Inventario::class);
    }

    public function kardex(): HasMany
    {
        return $this->hasMany(Kardex::class);
    }

    public function variantes(): HasMany
    {
        return $this->hasMany(Variante::class)->orderBy('color')->orderBy('id');
    }

    /**
     * Stock total sumando todas las variantes.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variantes->sum('stock');
    }

    /**
     * Primera variante disponible (con stock > 0), o la primera en general.
     */
    public function getDefaultVarianteAttribute(): ?Variante
    {
        return $this->variantes->firstWhere('stock', '>', 0)
            ?? $this->variantes->first();
    }

    protected static function booted()
    {
        static::creating(function ($producto) {
            //Si no se propociona un código, generar uno único
            if (empty($producto->codigo)) {
                $producto->codigo = self::generateUniqueCode();
            }
        });
    }

    /**
     * Genera un código único para el producto
     */
    private static function generateUniqueCode(): string
    {
        do {
            $code = str_pad(random_int(0, 9999999999), 12, '0', STR_PAD_LEFT);
        } while (self::where('codigo', $code)->exists());

        return $code;
    }

    /**
     * Accesor para obtener el código, nombre y presentación del producto
     */
    public function getNombreCompletoAttribute(): string
    {
        $sigla = $this->presentacione?->sigla ?? 'N/A';
        return "Código: {$this->codigo} - {$this->nombre} - Presentación: {$sigla}";
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->img_path)) return '';
        return $this->resolveImageUrl($this->img_path);
    }

    /**
     * Returns all image URLs: main + additional images
     */
    public function getTodasImagenesUrlsAttribute(): array
    {
        $urls = [];
        if (!empty($this->img_path)) {
            $urls[] = ['path' => $this->img_path, 'url' => $this->resolveImageUrl($this->img_path), 'main' => true];
        }
        foreach ($this->imagenes ?? [] as $path) {
            if (!empty($path)) {
                $urls[] = ['path' => $path, 'url' => $this->resolveImageUrl($path), 'main' => false];
            }
        }
        return $urls;
    }

    public function resolveImageUrl(string $path): string
    {
        if (str_starts_with($path, 'http')) return $path;

        $cloudName = config('filesystems.disks.cloudinary.cloud_name')
                  ?: parse_url($_ENV['CLOUDINARY_URL'] ?? '', PHP_URL_HOST)
                  ?: parse_url(getenv('CLOUDINARY_URL') ?: '', PHP_URL_HOST);

        if ($cloudName) {
            return "https://res.cloudinary.com/{$cloudName}/image/upload/{$path}";
        }

        // Nunca llamamos Storage::url() — con driver Cloudinary hace API call HTTP → timeout
        return '';
    }
}
