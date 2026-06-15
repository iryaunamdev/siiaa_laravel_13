<?php

namespace App\Models\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\EstudianteAsociado;
use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de visitante asociado a una solicitud.
 *
 * Una solicitud de tipo VISITA tiene un solo visitante principal.
 * Los requerimientos de visita cuelgan de este registro.
 */
class SolicitudVisitante extends Model
{
    protected $table = 'solicitudes_visitantes';

    protected $fillable = [
        'solicitud_id',
        'tipo_visitante_id',
        'estudiante_asociado_id',

        'nombre',
        'apellidos',
        'email',

        'pais_id',
        'institucion_id',
        'institucion',
        'lugar',

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

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function estudianteAsociado(): BelongsTo
    {
        return $this->belongsTo(EstudianteAsociado::class, 'estudiante_asociado_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones de catálogos
    |--------------------------------------------------------------------------
    */

    public function tipoVisitante(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'tipo_visitante_id');
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'pais_id');
    }

    public function institucionCatalogo(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'institucion_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers de tipo de visitante
    |--------------------------------------------------------------------------
    */

    public function tipoClave(): ?string
    {
        return $this->tipoVisitante?->clave;
    }

    public function esAcademico(): bool
    {
        return $this->tipoClave() === 'VACAD';
    }

    public function esEstudianteAsociado(): bool
    {
        return $this->tipoClave() === 'VEASOC';
    }

    public function esEstudianteNoAsociado(): bool
    {
        return $this->tipoClave() === 'VEST';
    }

    public function esOtro(): bool
    {
        return $this->tipoClave() === 'VOTRO';
    }

    /*
|--------------------------------------------------------------------------
| Auditoría institucional
|--------------------------------------------------------------------------
*/

    public function creador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'created_by');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers visuales
    |--------------------------------------------------------------------------
    */

    public function nombreCompleto(): string
    {
        if ($this->esEstudianteAsociado() && $this->estudianteAsociado) {
            return $this->estudianteAsociado->fullname ?? 'Estudiante asociado';
        }

        return trim(collect([
            $this->nombre,
            $this->apellidos,
        ])->filter()->implode(' ')) ?: 'Visitante sin nombre';
    }

    public function emailDisplay(): ?string
    {
        if ($this->esEstudianteAsociado() && $this->estudianteAsociado?->email) {
            return $this->estudianteAsociado->email;
        }

        return $this->email;
    }

    public function institucionDisplay(): ?string
    {
        return $this->institucion
            ?: $this->institucionCatalogo?->nombre;
    }

    public function periodoDisplay(): string
    {
        if (! $this->fecha_inicio && ! $this->fecha_fin) {
            return 'Sin fechas definidas';
        }

        if ($this->fecha_inicio && ! $this->fecha_fin) {
            return $this->fecha_inicio->format('d/m/Y');
        }

        if ($this->fecha_inicio?->equalTo($this->fecha_fin)) {
            return $this->fecha_inicio->format('d/m/Y');
        }

        return trim(collect([
            $this->fecha_inicio?->format('d/m/Y'),
            $this->fecha_fin?->format('d/m/Y'),
        ])->filter()->implode(' - '));
    }
}