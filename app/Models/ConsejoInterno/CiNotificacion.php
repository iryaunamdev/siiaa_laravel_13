<?php

namespace App\Models\ConsejoInterno;

use App\Models\IdentityLink;
use App\Models\Solicitudes\Solicitud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CiNotificacion extends Model
{
    protected $table = 'ci_notificaciones';

    public const TIPO_RESOLUCION_SOLICITUD = 'RESOLUCION_SOLICITUD';

    public const ESTATUS_PENDIENTE = 'PENDIENTE';
    public const ESTATUS_ENCOLADA = 'ENCOLADA';
    public const ESTATUS_ENVIADA = 'ENVIADA';
    public const ESTATUS_FALLIDA = 'FALLIDA';

    protected $fillable = [
        'reunion_id',
        'punto_id',
        'solicitud_id',
        'reenvio_de_id',
        'tipo',
        'resolucion',
        'destinatario_email',
        'destinatario_nombre',
        'destinatario_real_email',
        'destinatario_real_nombre',
        'es_prueba',
        'asunto',
        'payload',
        'adjuntos',
        'estatus',
        'queued_at',
        'sent_at',
        'failed_at',
        'intentos',
        'error',
        'created_by',
    ];

    protected $casts = [
        'payload'     => 'array',
        'adjuntos'    => 'array',
        'es_prueba'   => 'boolean',
        'queued_at'   => 'datetime',
        'sent_at'     => 'datetime',
        'failed_at'   => 'datetime',
        'intentos'    => 'integer',
    ];

    public function reunion(): BelongsTo
    {
        return $this->belongsTo(CiReunion::class, 'reunion_id');
    }

    public function punto(): BelongsTo
    {
        return $this->belongsTo(CiPunto::class, 'punto_id');
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function reenvioDe(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reenvio_de_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'created_by');
    }
}
