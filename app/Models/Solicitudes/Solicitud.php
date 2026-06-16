<?php

namespace App\Models\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Support\Solicitudes\SolicitudCatalogos;

/**
 * Modelo principal del módulo de solicitudes.
 *
 * Representa el expediente común de cualquier solicitud institucional.
 * Los datos específicos de visitantes, recursos, documentos y requerimientos
 * viven en tablas hijas.
 */
class Solicitud extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Claves de estatus
    |--------------------------------------------------------------------------
    |
    | Estas claves deben coincidir con catalogos_items.clave para el catálogo
    | institucional de estatus de solicitudes.
    |
    */
    public const ESTATUS_BORRADOR = SolicitudCatalogos::ESTATUS_BORRADOR;
    public const ESTATUS_ENVIADA = SolicitudCatalogos::ESTATUS_ENVIADA;
    public const ESTATUS_APROBADA_CI = SolicitudCatalogos::ESTATUS_APROBADA_CI;
    public const ESTATUS_RECHAZADA_CI = SolicitudCatalogos::ESTATUS_RECHAZADA_CI;
    public const ESTATUS_TRAMITE_PAGO = SolicitudCatalogos::ESTATUS_TRAMITE_PAGO;
    public const ESTATUS_PAGADA = SolicitudCatalogos::ESTATUS_PAGADA;
    public const ESTATUS_CERRADA = SolicitudCatalogos::ESTATUS_CERRADA;
    public const ESTATUS_CANCELADA = SolicitudCatalogos::ESTATUS_CANCELADA;

    protected $table = 'solicitudes';

    protected $fillable = [
        'folio',
        'folio_year',
        'folio_number',

        'owner_id',
        'created_by',
        'updated_by',

        'tipo_solicitud_id',
        'motivo_id',
        'motivo_otro',
        'requiere_recursos',

        'fecha_inicio',
        'fecha_fin',
        'pais_id',
        'nombre_evento',
        'tipo_presentacion',
        'institucion',
        'anfitrion',
        'lugar',
        'tutor_id',
        'informacion_adicional',

        'requiere_seguro_unam',
        'seguro_unam_beneficiario',

        'observaciones_sacad',
        'observaciones_administracion',

        'estatus_id',
        'submitted_at',
        'submitted_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'closed_at',
        'closed_by',
        'cancelled_at',
        'cancelled_by',

        'politica_aceptada_at',
        'politica_aceptada_by',
        'politica_version',

        'archived_at',
        'archived_by',
        'archive_reason',
    ];

    protected $casts = [
        'requiere_recursos'      => 'boolean',
        'requiere_seguro_unam'  => 'boolean',

        'fecha_inicio'          => 'date',
        'fecha_fin'             => 'date',

        'submitted_at'          => 'datetime',
        'approved_at'           => 'datetime',
        'rejected_at'           => 'datetime',
        'closed_at'             => 'datetime',
        'cancelled_at'          => 'datetime',

        'politica_aceptada_at'  => 'datetime',
        'archived_at'           => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones de identidad institucional
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Propietario institucional
    |--------------------------------------------------------------------------
    |
    | owner_id apunta a identity_links.id y representa al solicitante
    | propietario de la solicitud.
    |
    | No debe confundirse con users.id, personas.id ni estudiantes.id.
    | La identidad institucional activa se resuelve desde la capa de identidad
    | del SIIAA y se persiste aquí para trazabilidad.
    |
    */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'owner_id');
    }

    /**
     * Alias semántico de owner().
     *
     * Útil cuando se quiera expresar la relación en términos institucionales:
     * propietario de la solicitud = identity_links.id del solicitante.
     */
    public function propietario(): BelongsTo
    {
        return $this->owner();
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'created_by');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'updated_by');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'tutor_id');
    }

    public function politicaAceptadaPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'politica_aceptada_by');
    }

    public function enviadoPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'submitted_by');
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'approved_by');
    }

    public function rechazadoPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'rejected_by');
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'closed_by');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'cancelled_by');
    }

    public function archivador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'archived_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones de catálogos
    |--------------------------------------------------------------------------
    */

    public function tipoSolicitud(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'tipo_solicitud_id');
    }

    public function motivo(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'motivo_id');
    }

    public function estatus(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'estatus_id');
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'pais_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones hijas del expediente
    |--------------------------------------------------------------------------
    */

    public function visitante(): HasOne
    {
        return $this->hasOne(SolicitudVisitante::class, 'solicitud_id');
    }

    public function recursos(): HasMany
    {
        return $this->hasMany(SolicitudRecurso::class, 'solicitud_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(SolicitudDocumento::class, 'solicitud_id');
    }

    /**
     * Requerimientos asociados al expediente principal de la solicitud.
     *
     * Aunque por ahora aplican funcionalmente a solicitudes de visitantes,
     * pertenecen al expediente de la solicitud y no al registro específico
     * del visitante.
     */
    public function requerimientos(): HasMany
    {
        return $this->hasMany(SolicitudRequerimiento::class, 'solicitud_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeNoArchivadas(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchivadas(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeDelOwner(Builder $query, int $identityLinkId): Builder
    {
        return $query->where('owner_id', $identityLinkId);
    }

    public function scopePorFolio(Builder $query, ?string $folio): Builder
    {
        return $query->when($folio, function (Builder $query) use ($folio) {
            $query->where('folio', 'like', '%' . trim($folio) . '%');
        });
    }

    public function scopePorEstatus(Builder $query, ?int $estatusId): Builder
    {
        return $query->when($estatusId, function (Builder $query) use ($estatusId) {
            $query->where('estatus_id', $estatusId);
        });
    }

    public function scopePorTipo(Builder $query, ?int $tipoSolicitudId): Builder
    {
        return $query->when($tipoSolicitudId, function (Builder $query) use ($tipoSolicitudId) {
            $query->where('tipo_solicitud_id', $tipoSolicitudId);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de estado
    |--------------------------------------------------------------------------
    |
    | Estos métodos trabajan con la clave del catálogo relacionado.
    | Por eso conviene cargar la relación estatus cuando se usen en listados grandes.
    */

    public function estatusClave(): ?string
    {
        return $this->estatus?->clave;
    }

    public function esBorrador(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_BORRADOR);
    }

    public function estaEnviada(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_ENVIADA);
    }

    public function estaAprobadaCi(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_APROBADA_CI);
    }

    public function estaRechazadaCi(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_RECHAZADA_CI);
    }

    public function estaEnTramitePago(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_TRAMITE_PAGO);
    }

    public function estaPagada(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_PAGADA);
    }

    public function estaCerrada(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_CERRADA);
    }

    public function estaCancelada(): bool
    {
        return $this->tieneEstatus(self::ESTATUS_CANCELADA);
    }

    public function estaArchivada(): bool
    {
        return $this->archived_at !== null;
    }

    public function tieneFolio(): bool
    {
        return ! blank($this->folio)
            && ! is_null($this->folio_year)
            && ! is_null($this->folio_number);
    }

    /**
     * Determina si la solicitud tiene alguno de los estatus indicados.
     */
    public function tieneEstatus(string|array $claves): bool
    {
        $claves = is_array($claves) ? $claves : [$claves];

        return in_array($this->estatusClave(), $claves, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de edición
    |--------------------------------------------------------------------------
    */

    /**
     * La solicitud puede enviarse solo cuando está en borrador.
     */
    public function puedeEnviar(): bool
    {
        return $this->esBorrador();
    }

    /**
     * El propietario institucional solo puede editar mientras está en borrador.
     */
    public function puedeEditarPropietario(): bool
    {
        return $this->tieneEstatus(SolicitudCatalogos::estatusEditablesPorPropietario());
    }

    /**
     * El propietario puede cancelar solicitudes todavía no resueltas.
     */
    public function puedeCancelarPropietario(): bool
    {
        return $this->tieneEstatus([
            self::ESTATUS_BORRADOR,
            self::ESTATUS_ENVIADA,
        ]);
    }

    /**
     * SACAD/administración puede revisar solicitudes enviadas.
     */
    public function puedeRevisarse(): bool
    {
        return $this->estaEnviada();
    }

    /**
     * La solicitud puede pasar a trámite de pago después de aprobación CI.
     */
    public function puedePasarATramitePago(): bool
    {
        return $this->estaAprobadaCi();
    }

    /**
     * La solicitud puede marcarse como pagada si está en trámite de pago.
     */
    public function puedeMarcarsePagada(): bool
    {
        return $this->estaEnTramitePago();
    }

    /**
     * La solicitud puede cerrarse si ya fue aprobada, está en trámite o fue pagada.
     */
    public function puedeCerrarse(): bool
    {
        return $this->tieneEstatus([
            self::ESTATUS_APROBADA_CI,
            self::ESTATUS_TRAMITE_PAGO,
            self::ESTATUS_PAGADA,
        ]);
    }

    /**
     * Indica si la solicitud está en un estado terminal.
     */
    public function estaFinalizada(): bool
    {
        return $this->tieneEstatus([
            self::ESTATUS_RECHAZADA_CI,
            self::ESTATUS_CERRADA,
            self::ESTATUS_CANCELADA,
        ]);
    }

    public function estaBloqueadaParaPropietario(): bool
    {
        return ! $this->puedeEditarPropietario();
    }

    public function puedeEliminarPropietario(): bool
    {
        return $this->esBorrador();
    }

    public function requierePoliticaRecursos(): bool
    {
        return (bool) $this->requiere_recursos;
    }

    public function tienePoliticaAceptada(): bool
    {
        return ! is_null($this->politica_aceptada_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de tipo
    |--------------------------------------------------------------------------
    */

    public function tipoClave(): ?string
    {
        return $this->tipoSolicitud?->clave;
    }

    public function esVisitante(): bool
    {
        return $this->tipoClave() === SolicitudCatalogos::TIPO_VISITANTE;
    }

    public function esAusenciaConRecursos(): bool
    {
        return $this->tipoClave() === SolicitudCatalogos::TIPO_AUSENCIA_CON_RECURSOS;
    }

    public function esAusenciaSinRecursos(): bool
    {
        return $this->tipoClave() === SolicitudCatalogos::TIPO_AUSENCIA_SIN_RECURSOS;
    }

    public function esSoloRecursos(): bool
    {
        return $this->tipoClave() === SolicitudCatalogos::TIPO_SOLO_RECURSOS;
    }

    public function esRecursosIryaEstudiante(): bool
    {
        return $this->tipoClave() === SolicitudCatalogos::TIPO_RECURSOS_IRYA_ESTUDIANTE;
    }

    public function requiereMotivoOtro(): bool
    {
        return $this->motivoClave() === SolicitudCatalogos::MOTIVO_OTRO;
    }

    public function motivoClave(): ?string
    {
        return $this->motivo?->clave;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers visuales
    |--------------------------------------------------------------------------
    */

    public function folioDisplay(): string
    {
        return $this->folio ?: 'Sin Folio';
    }

    public function ownerNombre(): string
    {
        return $this->owner?->fullname() ?? 'Sin propietario';
    }

    /**
     * Determina si una identidad institucional es propietaria de la solicitud.
     */
    public function perteneceA(?int $identityLinkId): bool
    {
        return $identityLinkId !== null
            && (int) $this->owner_id === (int) $identityLinkId;
    }

    public function tutorNombre(): ?string
    {
        return $this->tutor?->fullname();
    }

    public function tieneRequerimientos(): bool
    {
        return $this->requerimientos()->exists();
    }

    public function nombresRequerimientos(): array
    {
        return $this->requerimientos
            ->map(fn(SolicitudRequerimiento $item) => $item->nombre())
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Nombre visible del estatus.
     */
    public function estatusNombre(): string
    {
        return $this->estatus?->nombre ?? 'Sin estatus';
    }

    /**
     * Clase visual sugerida para badges de estatus.
     */
    public function estatusBadgeClass(): string
    {
        return match ($this->estatusClave()) {
            self::ESTATUS_BORRADOR => 'bg-gray-100 text-gray-700 border-gray-200',
            self::ESTATUS_ENVIADA => 'bg-blue-100 text-blue-700 border-blue-200',
            self::ESTATUS_APROBADA_CI => 'bg-green-100 text-green-700 border-green-200',
            self::ESTATUS_RECHAZADA_CI => 'bg-red-100 text-red-700 border-red-200',
            self::ESTATUS_TRAMITE_PAGO => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::ESTATUS_PAGADA => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::ESTATUS_CERRADA => 'bg-slate-100 text-slate-700 border-slate-200',
            self::ESTATUS_CANCELADA => 'bg-zinc-100 text-zinc-600 border-zinc-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }
}