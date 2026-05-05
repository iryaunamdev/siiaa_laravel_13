<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $table = 'catalogos';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'activo',
        'meta',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'meta' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(CatalogoItem::class);
    }

    public function itemsActivos()
    {
        return $this->hasMany(CatalogoItem::class)
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre');
    }

    public function scopeByClave($query, string $clave)
    {
        return $query->where('clave', $clave);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
                ->orWhere('clave', 'like', "%{$term}%")
                ->orWhere('descripcion', 'like', "%{$term}%");
        });
    }

    public static function getItems(string $clave)
    {
        return static::byClave($clave)
            ->firstOrFail()
            ->itemsActivos
            ->map(fn($item) => [
                'value' => $item->clave,
                'label' => $item->nombre,
            ]);
    }
}
