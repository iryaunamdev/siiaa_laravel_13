<?php

namespace App\Models\Siiap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Catalogo extends Model
{
    protected $connection = 'siiap';

    protected $table = 'catalogos';

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function items(): HasMany
    {
        return $this->hasMany(CatalogoItem::class, 'catalogo_id');
    }

    public function activos(): HasMany
    {
        return $this->items()
            ->where('activo', true);
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

    public function scopePorClave(Builder $query, string $clave): Builder
    {
        return $query->where('clave', $clave);
    }

    public function scopeOrdenado(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}
