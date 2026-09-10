<?php

namespace App\Models\ConsejoInterno;

use App\Models\IdentityLink;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Acta del Consejo Interno.
 *
 * Es una entidad autónoma. Puede asociarse opcionalmente a una reunión,
 * pero no hereda documentos ni datos operativos de la reunión.
 */
class CiActa extends Model
{
    protected $table = 'ci_actas';

    protected $fillable = [
        'reunion_id',
        'numero_acta',
        'titulo',
        'fecha',
        'year',
        'estatus',
        'contenido_json',
        'contenido_html',
        'search_text',
        'publicada_at',
        'publicada_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha'          => 'date',
        'year'           => 'integer',
        'contenido_json' => 'array',
        'publicada_at'   => 'datetime',
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

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(CiActaEvaluacion::class, 'acta_id');
    }

    public function publicadaPor(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'publicada_by');
    }

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
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeBorrador(Builder $query): Builder
    {
        return $query->where('estatus', ConsejoInternoCatalogos::ACTA_BORRADOR);
    }

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->where('estatus', ConsejoInternoCatalogos::ACTA_PUBLICADA);
    }

    public function scopeDelYear(Builder $query, ?int $year): Builder
    {
        if (blank($year)) {
            return $query;
        }

        return $query->where('year', $year);
    }

    public function scopeBuscar(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search) {
            $query->where('numero_acta', 'like', "%{$search}%")
                ->orWhere('titulo', 'like', "%{$search}%")
                ->orWhere('search_text', 'like', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de estado
    |--------------------------------------------------------------------------
    */

    public function estaEnBorrador(): bool
    {
        return $this->estatus === ConsejoInternoCatalogos::ACTA_BORRADOR;
    }

    public function estaPublicada(): bool
    {
        return $this->estatus === ConsejoInternoCatalogos::ACTA_PUBLICADA;
    }

    public function tieneReunion(): bool
    {
        return filled($this->reunion_id);
    }
}