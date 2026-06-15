<?php

namespace App\Models;

use App\Models\Solicitudes\SolicitudVisitante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de estudiantes asociados.
 *
 * Representa estudiantes externos o asociados al IRyA que pueden registrarse
 * principalmente dentro del flujo de solicitudes de visitante.
 *
 * No deben confundirse con estudiantes SIIAP adscritos al IRyA.
 */
class EstudianteAsociado extends Model
{
    use SoftDeletes;

    protected $table = 'estudiantes_asociados';

    protected $fillable = [
        'nombre',
        'apellidop',
        'apellidom',
        'email',
        'curp',
        'rfc',
        'fecha_nacimiento',
        'sexo_id',
        'nacionalidad_id',
        'photo_url',
        'activo',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo'           => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones principales
    |--------------------------------------------------------------------------
    */

    public function ingresos(): HasMany
    {
        return $this->hasMany(EstudianteAsociadoIngreso::class, 'estudiante_id');
    }

    public function solicitudesVisitante(): HasMany
    {
        return $this->hasMany(SolicitudVisitante::class, 'estudiante_asociado_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Catálogos
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

    /*
    |--------------------------------------------------------------------------
    | Auditoría institucional
    |--------------------------------------------------------------------------
    |
    | created_by y updated_by apuntan a identity_links.id.
    |
    */

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'created_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('activo', false);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        return $query->when($search !== '', function (Builder $query) use ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellidop', 'like', "%{$search}%")
                    ->orWhere('apellidom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('curp', 'like', "%{$search}%")
                    ->orWhere('rfc', 'like', "%{$search}%");
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors / helpers visuales
    |--------------------------------------------------------------------------
    */

    public function getFullnameAttribute(): string
    {
        return trim(collect([
            $this->nombre,
            $this->apellidop,
            $this->apellidom,
        ])->filter()->implode(' '));
    }

    public function getInitialsAttribute(): string
    {
        $partes = collect([
            $this->nombre,
            $this->apellidop,
        ])
            ->filter()
            ->map(fn($value) => mb_substr(trim($value), 0, 1))
            ->implode('');

        return mb_strtoupper($partes ?: 'EA');
    }

    public function nombreDisplay(): string
    {
        return $this->fullname ?: 'Estudiante asociado sin nombre';
    }

    public function emailDisplay(): ?string
    {
        return $this->email;
    }

    public function activoDisplay(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    /*
    |--------------------------------------------------------------------------
    | Periodos / ingresos
    |--------------------------------------------------------------------------
    */

    public function periodoActual(): ?EstudianteAsociadoIngreso
    {
        return $this->ingresos()
            ->where(function (Builder $query) {
                $query->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '>=', now()->toDateString());
            })
            ->orderByDesc('fecha_inicio')
            ->first();
    }

    public function ultimoIngreso(): ?EstudianteAsociadoIngreso
    {
        return $this->ingresos()
            ->orderByDesc('fecha_inicio')
            ->first();
    }

    public function tieneSolicitudes(): bool
    {
        return $this->solicitudesVisitante()->exists();
    }

    public function puedeEliminarseFisicamente(): bool
    {
        return ! $this->tieneSolicitudes();
    }
}