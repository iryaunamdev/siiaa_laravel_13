<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de ingresos o periodos académicos de estudiantes asociados.
 *
 * El tutor institucional se resuelve mediante identity_links.id.
 * No debe apuntar a personas.id ni users.id.
 */
class EstudianteAsociadoIngreso extends Model
{
    protected $table = 'estudiantes_asociados_ingresos';

    protected $fillable = [
        'estudiante_id',
        'tutor_id',
        'grado_id',
        'tipo_id',
        'universidad_id',
        'fecha_inicio',
        'fecha_fin',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones principales
    |--------------------------------------------------------------------------
    */

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(EstudianteAsociado::class, 'estudiante_id');
    }

    /**
     * Tutor institucional.
     *
     * Apunta a identity_links.id.
     */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'tutor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Catálogos
    |--------------------------------------------------------------------------
    */

    public function grado(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'grado_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'tipo_id');
    }

    public function universidad(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'universidad_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Auditoría institucional
    |--------------------------------------------------------------------------
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

    public function scopeVigentes(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('fecha_fin')
                ->orWhereDate('fecha_fin', '>=', now()->toDateString());
        });
    }

    public function scopeConcluidos(Builder $query): Builder
    {
        return $query->whereNotNull('fecha_fin')
            ->whereDate('fecha_fin', '<', now()->toDateString());
    }

    public function scopeOrdenReciente(Builder $query): Builder
    {
        return $query->orderByDesc('fecha_inicio');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de periodo
    |--------------------------------------------------------------------------
    */

    public function estaVigente(): bool
    {
        return is_null($this->fecha_fin)
            || $this->fecha_fin->greaterThanOrEqualTo(now()->startOfDay());
    }

    public function estaConcluido(): bool
    {
        return ! $this->estaVigente();
    }

    public function periodoDisplay(): string
    {
        if (! $this->fecha_inicio && ! $this->fecha_fin) {
            return 'Sin periodo definido';
        }

        if ($this->fecha_inicio && ! $this->fecha_fin) {
            return $this->fecha_inicio->format('d/m/Y') . ' - vigente';
        }

        if ($this->fecha_inicio?->equalTo($this->fecha_fin)) {
            return $this->fecha_inicio->format('d/m/Y');
        }

        return trim(collect([
            $this->fecha_inicio?->format('d/m/Y'),
            $this->fecha_fin?->format('d/m/Y'),
        ])->filter()->implode(' - '));
    }

    public function tutorNombre(): ?string
    {
        return $this->tutor?->fullname();
    }

    public function gradoNombre(): ?string
    {
        return $this->grado?->nombre;
    }

    public function tipoNombre(): ?string
    {
        return $this->tipo?->nombre;
    }

    public function universidadNombre(): ?string
    {
        return $this->universidad?->nombre;
    }

    /*
    |--------------------------------------------------------------------------
    | Reglas operativas
    |--------------------------------------------------------------------------
    */

    public function puedeEditarUsuarioGeneral(?int $identityLinkId = null): bool
    {
        if ($this->estaConcluido()) {
            return false;
        }

        if (is_null($identityLinkId)) {
            return false;
        }

        return (int) $this->created_by === (int) $identityLinkId;
    }
}