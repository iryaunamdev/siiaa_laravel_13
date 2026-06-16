<?php

namespace App\Http\Controllers\Solicitudes;

use App\Http\Controllers\Controller;
use App\Models\Solicitudes\SolicitudDocumento;
use Illuminate\Support\Facades\Storage;

class SolicitudDocumentoDownloadController extends Controller
{
    public function __invoke(SolicitudDocumento $documento)
    {
        $this->authorize('view', $documento->solicitud);

        abort_if(
            blank($documento->path) || ! Storage::disk('local')->exists($documento->path),
            404
        );

        return Storage::disk('local')->download(
            $documento->path,
            $documento->original_name ?: $documento->filename
        );
    }
}
