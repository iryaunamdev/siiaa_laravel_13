<?php

namespace App\Models\ConsejoInterno;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use App\Models\Solicitudes\Solicitud;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Punto del orden del día del Consejo Interno.
 *
 * Puede representar una solicitud enviada al Consejo Interno o un punto libre
 * agregado por SACAD para revisión interna.
 */
class CiPunto extends Model
{
    protected $table = 'ci_puntos';

    protected $fillable = [
        'reunion_id',
        'tipo_punto_id',
        'solicitud_id',
        'titulo',
        'descripcion',
        'orden',
        'resolucion',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'orden'       => 'integer',
        'resolved_at' => 'datetime',
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

    public function tipoPunto(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'tipo_punto_id');
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(CiPuntoEvaluacion::class, 'punto_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'resolved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSolicitudes(Builder $query): Builder
    {
        return $query->whereHas('tipoPunto', function (Builder $query) {
            $query->where('clave', ConsejoInternoCatalogos::TIPO_PUNTO_SOLICITUD);
        });
    }

    public function scopeOtros(Builder $query): Builder
    {
        return $query->whereHas('tipoPunto', function (Builder $query) {
            $query->where('clave', ConsejoInternoCatalogos::TIPO_PUNTO_OTRO);
        });
    }

    public function scopePendientesResolucion(Builder $query): Builder
    {
        return $query->whereNull('resolucion');
    }

    public function scopeResueltos(Builder $query): Builder
    {
        return $query->whereNotNull('resolucion');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de tipo
    |--------------------------------------------------------------------------
    */

    public function esSolicitud(): bool
    {
        return $this->tipoPunto?->clave === ConsejoInternoCatalogos::TIPO_PUNTO_SOLICITUD;
    }

    public function esOtroPunto(): bool
    {
        return $this->tipoPunto?->clave === ConsejoInternoCatalogos::TIPO_PUNTO_OTRO;
    }

    public function estaResuelto(): bool
    {
        return filled($this->resolucion);
    }

    public function estaPendienteResolucion(): bool
    {
        return blank($this->resolucion);
    }
}