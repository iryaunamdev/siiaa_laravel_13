<?php

namespace App\Models\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de recursos solicitados.
 *
 * Una solicitud puede tener uno o más recursos asociados.
 * La política de recursos/viáticos vive en la solicitud principal,
 * no en cada recurso.
 */
class SolicitudRecurso extends Model
{
    protected $table = 'solicitudes_recursos';

    protected $fillable = [
        'solicitud_id',

        'origen_id',
        'proyecto_id',
        'proyecto_nombre',

        'dias_n',
        'dias_i',

        'cuota',
        'cuota_divisa',

        'avion',
        'avion_divisa',

        'otro',
        'otro_divisa',

        'informacion_adicional',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'proyecto_id' => 'integer',

        'dias_n' => 'integer',
        'dias_i' => 'integer',

        'cuota' => 'decimal:2',
        'avion' => 'decimal:2',
        'otro'  => 'decimal:2',
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

    /*
    |--------------------------------------------------------------------------
    | Relaciones de catálogos
    |--------------------------------------------------------------------------
    */

    public function origen(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'origen_id');
    }

    public function cuotaDivisa(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'cuota_divisa');
    }

    public function avionDivisa(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'avion_divisa');
    }

    public function otroDivisa(): BelongsTo
    {
        return $this->belongsTo(CatalogoItem::class, 'otro_divisa');
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
    | Helpers de montos
    |--------------------------------------------------------------------------
    */

    public function tieneCuota(): bool
    {
        return ! is_null($this->cuota) && (float) $this->cuota > 0;
    }

    public function tieneAvion(): bool
    {
        return ! is_null($this->avion) && (float) $this->avion > 0;
    }

    public function tieneOtro(): bool
    {
        return ! is_null($this->otro) && (float) $this->otro > 0;
    }

    public function tieneAlgunoMonto(): bool
    {
        return $this->tieneCuota()
            || $this->tieneAvion()
            || $this->tieneOtro();
    }

    public function cuotaDisplay(): ?string
    {
        return $this->montoDisplay($this->cuota, $this->cuotaDivisa);
    }

    public function avionDisplay(): ?string
    {
        return $this->montoDisplay($this->avion, $this->avionDivisa);
    }

    public function otroDisplay(): ?string
    {
        return $this->montoDisplay($this->otro, $this->otroDivisa);
    }

    protected function montoDisplay(mixed $monto, ?CatalogoItem $divisa): ?string
    {
        if (is_null($monto) || (float) $monto <= 0) {
            return null;
        }

        return number_format((float) $monto, 2) . ' ' . ($divisa?->clave ?? '');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers visuales
    |--------------------------------------------------------------------------
    */

    public function origenDisplay(): string
    {
        return $this->origen?->nombre ?? 'Sin origen definido';
    }

    public function proyectoDisplay(): string
    {
        return $this->proyecto_nombre ?: 'Sin proyecto definido';
    }

    public function diasDisplay(): string
    {
        $partes = [];

        if (! is_null($this->dias_n)) {
            $partes[] = "Nacionales: {$this->dias_n}";
        }

        if (! is_null($this->dias_i)) {
            $partes[] = "Internacionales: {$this->dias_i}";
        }

        return $partes
            ? implode(' / ', $partes)
            : 'Sin días definidos';
    }
}