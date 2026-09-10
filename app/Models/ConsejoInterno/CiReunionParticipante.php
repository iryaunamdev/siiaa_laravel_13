<?php

namespace App\Models\ConsejoInterno;

use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Participante de una reunión del Consejo Interno.
 *
 * Registra qué identidad institucional forma parte de una reunión específica.
 */
class CiReunionParticipante extends Model
{
    protected $table = 'ci_reuniones_participantes';

    protected $fillable = [
        'reunion_id',
        'identity_link_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function reunion(): BelongsTo
    {
        return $this->belongsTo(CiReunion::class, 'reunion_id');
    }

    public function identidad(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'identity_link_id');
    }
}