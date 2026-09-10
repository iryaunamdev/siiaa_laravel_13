<?php

namespace App\Models\ConsejoInterno;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Evaluación vigente de un consejero sobre un punto del Consejo Interno.
 *
 * La evaluación es una opinión interna y no equivale a la resolución final
 * del punto.
 */
class CiPuntoEvaluacion extends Model
{
    protected $table = 'ci_puntos_evaluaciones';

    protected $fillable = [
        'punto_id',
        'identity_link_id',
        'evaluacion_id',
        'comentarios',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function punto(): BelongsTo
    {
        return $this->belongsTo(CiPunto::class, 'punto_id');
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