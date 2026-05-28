<?php

namespace App\Models\Siiap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EstudianteInscripcion extends Model
{
    protected $connection = 'siiap';

    protected $table = 'estudiantes_inscripciones';

    protected $appends = [
        'comite_tutor',
        'tutor_principal',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getComiteTutorAttribute(): Collection
    {
        return $this->comite;
    }

    public function getTutorPrincipalAttribute(): ?EstudianteTutor
    {
        return $this->comite
            ->first(function (EstudianteTutor $estudianteTutor) {
                return (bool) $estudianteTutor->principal === true;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(EstudianteIngreso::class, 'ingreso_id');
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'semestre_id');
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'grado_id');
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'programa_id');
    }

    public function adscripcion(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'adscripcion_id');
    }

    public function comite(): HasMany
    {
        return $this->hasMany(EstudianteTutor::class, 'inscripcion_id')
            ->with([
                'tutor',
                'tutor.grado',
                'tutor.adscripcion',
                'tutor.sni',
                'tutor.pride',
                'tutor.contrato',
            ]);
    }

    public function tutorPrincipal(): HasOne
    {
        return $this->hasOne(EstudianteTutor::class, 'inscripcion_id')
            ->where('principal', true)
            ->with([
                'tutor',
                'tutor.grado',
                'tutor.adscripcion',
                'tutor.sni',
                'tutor.pride',
                'tutor.contrato',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes autorizados
    |--------------------------------------------------------------------------
    */

    public function scopeAdscripcionIrya(Builder $query): Builder
    {
        return $query->whereHas('adscripcion', function (Builder $query) {
            $query->where('clave', 'IRyA');
        });
    }

    public function scopeSemestresRecientes(Builder $query): Builder
    {
        $semestres = ultimosTresSemestres(currentSemestre());

        return $query->whereHas('semestre', function (Builder $query) use ($semestres) {
            $query->whereIn('nombre', $semestres);
        });
    }

    public function scopeActivasIrya(Builder $query): Builder
    {
        $semestres = ultimosTresSemestres(currentSemestre());

        return $query
            ->whereHas('adscripcion', function (Builder $query) {
                $query->where('clave', 'IRyA');
            })
            ->whereHas('semestre', function (Builder $query) use ($semestres) {
                $query->whereIn('nombre', $semestres);
            });
    }

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

    public function scopePorSemestre(Builder $query, string $semestre): Builder
    {
        return $query->whereHas('semestre', function (Builder $query) use ($semestre) {
            $query->where('nombre', $semestre);
        });
    }
}
