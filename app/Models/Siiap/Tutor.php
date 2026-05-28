<?php

namespace App\Models\Siiap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutor extends Model
{
    protected $connection = 'siiap';

    protected $table = 'tutores';

    protected $appends = [
        'fullname',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullnameAttribute(): string
    {
        return trim(
            ($this->titulo ?? '') . ' ' .
                ($this->nombre ?? '') . ' ' .
                ($this->apellidop ?? '') . ' ' .
                ($this->apellidom ?? '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function grado(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'grado_id');
    }

    public function adscripcion(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'adscripcion_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'contrato_id');
    }

    public function sni(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'sni_id');
    }

    public function pride(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'pride_id');
    }

    public function comites(): HasMany
    {
        return $this->hasMany(EstudianteTutor::class, 'tutor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAdscripcionIrya(Builder $query): Builder
    {
        return $query->whereHas('adscripcion', function (Builder $query) {
            $query->where('clave', 'IRyA');
        });
    }

    public function scopeOrdenadoPorNombre(Builder $query): Builder
    {
        return $query
            ->orderBy('apellidop')
            ->orderBy('apellidom')
            ->orderBy('nombre');
    }
}
