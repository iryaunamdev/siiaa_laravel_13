<?php

namespace App\Models\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de requerimientos asociados al expediente principal de una solicitud.
 *
 * Aunque por ahora los requerimientos aplican funcionalmente a solicitudes
 * de visitantes, la relación pertenece a solicitudes para mantener el diseño
 * institucional aprobado: los requerimientos son parte del expediente, no del
 * registro específico del visitante.
 */
class SolicitudRequerimiento extends Model
{
    protected $table = 'solicitudes_requerimientos';

    protected $fillable = [
        'solicitud_id',
        'requerimiento_id',
        'created_by',
        'updated_by',
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

    public function requerimiento(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'requerimiento_id');
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

    public function clave(): ?string
    {
        return $this->requerimiento?->clave;
    }

    public function nombre(): string
    {
        return $this->requerimiento?->nombre ?? 'Requerimiento sin nombre';
    }

    public function descripcion(): ?string
    {
        return $this->requerimiento?->descripcion;
    }
}