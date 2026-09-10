<?php

namespace App\Models\ConsejoInterno;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Evaluación vigente de una identidad institucional sobre un acta.
 *
 * La evaluación del acta ayuda a SACAD a revisar el contenido, pero no bloquea
 * obligatoriamente la publicación.
 */
class CiActaEvaluacion extends Model
{
    protected $table = 'ci_actas_evaluaciones';

    protected $fillable = [
        'acta_id',
        'identity_link_id',
        'evaluacion_id',
        'comentarios',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function acta(): BelongsTo
    {
        return $this->belongsTo(CiActa::class, 'acta_id');
    }

    public function identidad(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'identity_link_id');
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'evaluacion_id');
    }
}