<?php

namespace App\Http\Controllers\Solicitudes;

use App\Http\Controllers\Controller;
use App\Models\Solicitudes\SolicitudDocumento;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class SolicitudDocumentoDownloadController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(SolicitudDocumento $documento)
    {
        $this->authorize('view', $documento->solicitud);

        $disk = config('filesystems.default', 'local');

        abort_if(
            blank($documento->path) || ! Storage::disk($disk)->exists($documento->path),
            404
        );

        return Storage::disk($disk)->download(
            $documento->path,
            $documento->original_name ?: $documento->filename
        );
    }
}
