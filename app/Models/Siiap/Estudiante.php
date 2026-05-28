<?php

namespace App\Models\Siiap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Estudiante extends Model
{
    use HasFactory;

    protected $connection = 'siiap';

    protected $table = 'estudiantes';

    protected $appends = [
        'fullname',
        'activo',
        'inscripcion_actual',
        'grado_actual_clave',
        'grado_actual_nombre',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullnameAttribute(): string
    {
        return trim(
            ($this->apellidop ?? '') . ' ' .
                ($this->apellidom ?? '') . ' ' .
                ($this->nombre ?? '')
        );
    }

    public function getActivoAttribute(): bool
    {
        $semestres = ultimosTresSemestres(currentSemestre());

        return $this->inscripciones()
            ->whereHas('adscripcion', function (Builder $query) {
                $query->where('clave', 'IRyA');
            })
            ->whereHas('semestre', function (Builder $query) use ($semestres) {
                $query->whereIn('nombre', $semestres);
            })
            ->exists();
    }

    public function getInscripcionActualAttribute(): ?EstudianteInscripcion
    {
        $semestres = ultimosTresSemestres(currentSemestre());

        return $this->inscripciones()
            ->whereHas('adscripcion', function (Builder $query) {
                $query->where('clave', 'IRyA');
            })
            ->whereHas('semestre', function (Builder $query) use ($semestres) {
                $query->whereIn('nombre', $semestres);
            })
            ->with([
                'semestre',
                'grado',
                'programa',
                'adscripcion',
                'comite.tutor',
                'comite.tutor.grado',
                'comite.tutor.adscripcion',
                'comite.tutor.sni',
                'comite.tutor.pride',
                'comite.tutor.contrato',
            ])
            ->get()
            ->sortByDesc(function (EstudianteInscripcion $inscripcion) {
                return semestreOrdenValue($inscripcion->semestre?->nombre);
            })
            ->first();
    }

    /**
     * Iniciales para avatares o identificadores visuales.
     *
     * Prioriza nombre y apellido paterno. Si alguno falta, toma las iniciales
     * disponibles sin romper la vista.
     */
    public function getInitialsAttribute(): string
    {
        $parts = collect([
            $this->nombre,
            $this->apellidop,
        ])
            ->filter()
            ->map(fn($value) => Str::of(trim((string) $value))->substr(0, 1));

        return Str::of($parts->implode(''))
            ->upper()
            ->ascii()
            ->toString();
    }

    public function getGradoActualAttribute(): ?CatalogoItem
    {
        return $this->inscripcion_actual?->grado;
    }

    public function getGradoActualClaveAttribute(): ?string
    {
        return $this->grado_actual?->clave;
    }

    public function getGradoActualNombreAttribute(): ?string
    {
        return $this->grado_actual?->nombre;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function sexo(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'sexo_id');
    }

    public function nacionalidad(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'nacionalidad_id');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(EstudianteIngreso::class, 'estudiante_id');
    }

    public function ingresoMae(): HasOne
    {
        return $this->hasOne(EstudianteIngreso::class, 'estudiante_id')
            ->whereHas('grado', function (Builder $query) {
                $query->where('clave', 'MAE');
            });
    }

    public function ingresoDoc(): HasOne
    {
        return $this->hasOne(EstudianteIngreso::class, 'estudiante_id')
            ->whereHas('grado', function (Builder $query) {
                $query->where('clave', 'DOC');
            });
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(EstudianteInscripcion::class, 'estudiante_id')
            ->with([
                'semestre' => function ($query) {
                    $query->orderBy('nombre', 'desc');
                },
                'grado',
                'programa',
                'adscripcion',
            ]);
    }

    public function inscripcionesMae(): HasMany
    {
        return $this->hasMany(EstudianteInscripcion::class, 'estudiante_id')
            ->whereHas('grado', function (Builder $query) {
                $query->where('clave', 'MAE');
            })
            ->with([
                'semestre' => function ($query) {
                    $query->orderBy('nombre', 'desc');
                },
                'grado',
                'programa',
                'adscripcion',
            ]);
    }

    public function inscripcionesDoc(): HasMany
    {
        return $this->hasMany(EstudianteInscripcion::class, 'estudiante_id')
            ->whereHas('grado', function (Builder $query) {
                $query->where('clave', 'DOC');
            })
            ->with([
                'semestre' => function ($query) {
                    $query->orderBy('nombre', 'desc');
                },
                'grado',
                'programa',
                'adscripcion',
            ]);
    }

    public function inscripcionIrya(): HasOne
    {
        return $this->hasOne(EstudianteInscripcion::class, 'estudiante_id')
            ->whereHas('adscripcion', function (Builder $query) {
                $query->where('clave', 'IRyA');
            })
            ->latest('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes autorizados
    |--------------------------------------------------------------------------
    */

    public function scopeConAdscripcionIrya(Builder $query): Builder
    {
        return $query->whereHas('inscripciones.adscripcion', function (Builder $query) {
            $query->where('clave', 'IRyA');
        });
    }

    public function scopeConInscripcionReciente(Builder $query): Builder
    {
        $semestres = ultimosTresSemestres(currentSemestre());

        return $query->whereHas('inscripciones.semestre', function (Builder $query) use ($semestres) {
            $query->whereIn('nombre', $semestres);
        });
    }

    public function scopeActivosIrya(Builder $query): Builder
    {
        $semestres = ultimosTresSemestres(currentSemestre());

        return $query->whereHas('inscripciones', function (Builder $query) use ($semestres) {
            $query->whereHas('adscripcion', function (Builder $query) {
                $query->where('clave', 'IRyA');
            })
                ->whereHas('semestre', function (Builder $query) use ($semestres) {
                    $query->whereIn('nombre', $semestres);
                });
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
