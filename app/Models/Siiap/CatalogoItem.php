<?php

namespace App\Models\Siiap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogoItem extends Model
{
    protected $connection = 'siiap';

    protected $table = 'catalogos_items';

    protected $casts = [
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopePorCatalogo(Builder $query, string $catalogoClave): Builder
    {
        return $query->whereHas('catalogo', function (Builder $query) use ($catalogoClave) {
            $query->where('clave', $catalogoClave);
        });
    }

    public function scopePorClave(Builder $query, string $clave): Builder
    {
        return $query->where('clave', $clave);
    }

    public function scopeOrdenado(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}
