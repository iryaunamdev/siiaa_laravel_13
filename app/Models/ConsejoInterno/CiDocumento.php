<?php

namespace App\Models\ConsejoInterno;

use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Documento anexo de una reunión del Consejo Interno.
 *
 * Los documentos pertenecen a la reunión. No forman parte de las actas.
 */
class CiDocumento extends Model
{
    protected $table = 'ci_documentos';

    protected $fillable = [
        'reunion_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
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

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'uploaded_by');
    }
}