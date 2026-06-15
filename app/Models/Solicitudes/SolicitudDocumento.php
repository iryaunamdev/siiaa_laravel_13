<?php

namespace App\Models\Solicitudes;

use App\Models\IdentityLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Modelo de documentos adjuntos a solicitudes.
 *
 * Los documentos son flexibles: no requieren clasificación manual
 * en la primera versión del módulo. El sistema almacena metadatos
 * básicos del archivo subido.
 */
class SolicitudDocumento extends Model
{
    protected $table = 'solicitudes_documentos';

    protected $fillable = [
        'solicitud_id',
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
    | Relaciones principales
    |--------------------------------------------------------------------------
    */

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'uploaded_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de archivo
    |--------------------------------------------------------------------------
    */

    public function exists(string $disk = 'public'): bool
    {
        return filled($this->path) && Storage::disk($disk)->exists($this->path);
    }

    public function url(string $disk = 'public'): ?string
    {
        if (blank($this->path)) {
            return null;
        }

        return Storage::disk($disk)->url($this->path);
    }

    public function deletePhysicalFile(string $disk = 'public'): bool
    {
        if (! $this->exists($disk)) {
            return false;
        }

        return Storage::disk($disk)->delete($this->path);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers visuales
    |--------------------------------------------------------------------------
    */

    public function nombreDisplay(): string
    {
        return $this->original_name ?: $this->filename;
    }

    public function sizeDisplay(): string
    {
        if (is_null($this->size)) {
            return 'Tamaño no disponible';
        }

        $bytes = (int) $this->size;

        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return number_format($bytes / (1024 * 1024), 1) . ' MB';
    }

    public function extension(): ?string
    {
        return pathinfo($this->filename ?: $this->original_name, PATHINFO_EXTENSION) ?: null;
    }

    public function uploadedByNombre(): ?string
    {
        return $this->uploadedBy?->fullname();
    }
}