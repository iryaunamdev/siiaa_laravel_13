<?php

namespace App\Models\Siiap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstudianteIngreso extends Model
{
    protected $connection = 'siiap';

    protected $table = 'estudiantes_ingresos';

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(EstudianteInscripcion::class, 'ingreso_id');
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'semestre_id');
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'grado_id');
    }

    public function universidad(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'universidad_id');
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'programa_id');
    }

    public function procedencia(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'procedencia_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes autorizados
    |--------------------------------------------------------------------------
    */

    public function scopeMaestria(Builder $query): Builder
    {
        return $query->whereHas('grado', function (Builder $query) {
            $query->where('clave', 'MAE');
        });
    }

    public function scopeDoctorado(Builder $query): Builder
    {
        return $query->whereHas('grado', function (Builder $query) {
            $query->where('clave', 'DOC');
        });
    }

    public function scopePorGrado(Builder $query, string $clave): Builder
    {
        return $query->whereHas('grado', function (Builder $query) use ($clave) {
            $query->where('clave', $clave);
        });
    }
}
