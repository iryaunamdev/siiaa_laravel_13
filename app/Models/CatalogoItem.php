<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoItem extends Model
{
    protected $table = 'catalogos_items';

    protected $fillable = [
        'catalogo_id',
        'orden',
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

    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
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
}
