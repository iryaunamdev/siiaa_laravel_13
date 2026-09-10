<?php

namespace App\Models\ConsejoInterno;

use App\Models\IdentityLink;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Reunión del Consejo Interno.
 *
 * Representa el expediente operativo donde SACAD organiza participantes,
 * solicitudes, otros puntos, documentos y resoluciones del Consejo Interno.
 */
class CiReunion extends Model
{
    protected $table = 'ci_reuniones';

    protected $fillable = [
        'titulo',
        'fecha',
        'tipo_reunion',
        'modalidad',
        'estatus',
        'created_by',
        'updated_by',
        'concluida_at',
        'concluida_by',
    ];

    protected $casts = [
        'fecha'        => 'date',
        'concluida_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function participantes(): HasMany
    {
        return $this->hasMany(CiReunionParticipante::class, 'reunion_id');
    }

    public function puntos(): HasMany
    {
        return $this->hasMany(CiPunto::class, 'reunion_id')
            ->orderBy('tipo_punto_id')
            ->orderBy('orden');
    }

    public function puntosSolicitud(): HasMany
    {
        return $this->hasMany(CiPunto::class, 'reunion_id')
            ->whereHas('tipoPunto', function (Builder $query) {
                $query->where('clave', ConsejoInternoCatalogos::TIPO_PUNTO_SOLICITUD);
            })
            ->orderBy('orden');
    }

    public function puntosOtros(): HasMany
    {
        return $this->hasMany(CiPunto::class, 'reunion_id')
            ->whereHas('tipoPunto', function (Builder $query) {
                $query->where('clave', ConsejoInternoCatalogos::TIPO_PUNTO_OTRO);
            })
            ->orderBy('orden');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(CiDocumento::class, 'reunion_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(
            CiNotificacion::class,
            'reunion_id'
        )->latest('id');
    }

    public function actas(): HasMany
    {
        return $this->hasMany(CiActa::class, 'reunion_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'created_by');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'updated_by');
    }

    public function concluidaPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'concluida_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeEnProceso(Builder $query): Builder
    {
        return $query->where('estatus', ConsejoInternoCatalogos::REUNION_EN_PROCESO);
    }

    public function scopeConcluidas(Builder $query): Builder
    {
        return $query->where('estatus', ConsejoInternoCatalogos::REUNION_CONCLUIDA);
    }

    public function scopeBuscar(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search) {
            $query->where('titulo', 'like', "%{$search}%")
                ->orWhere('tipo_reunion', 'like', "%{$search}%")
                ->orWhere('modalidad', 'like', "%{$search}%");
        });
    }

    public function scopeDelParticipante(Builder $query, int $identityLinkId): Builder
    {
        return $query->whereHas('participantes', function (Builder $query) use ($identityLinkId) {
            $query->where('identity_link_id', $identityLinkId);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de estado
    |--------------------------------------------------------------------------
    */

    public function estaEnProceso(): bool
    {
        return $this->estatus === ConsejoInternoCatalogos::REUNION_EN_PROCESO;
    }

    public function estaConcluida(): bool
    {
        return $this->estatus === ConsejoInternoCatalogos::REUNION_CONCLUIDA;
    }
}
