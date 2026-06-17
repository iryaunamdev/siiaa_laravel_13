<?php

use App\Http\Controllers\Solicitudes\SolicitudDocumentoDownloadController;
use App\Livewire\Solicitudes\SolicitudesCreate;
use App\Livewire\Solicitudes\SolicitudesEdit;
use App\Livewire\Solicitudes\SolicitudesIndex;
use App\Livewire\Solicitudes\SolicitudesShow;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])
    ->prefix('solicitudes')
    ->name('solicitudes.')
    ->group(function () {
        Route::get('/', SolicitudesIndex::class)
            ->name('index');

        Route::get('/documentos/{documento}/descargar', SolicitudDocumentoDownloadController::class)
            ->name('documentos.download');

        Route::get('/crear', SolicitudesCreate::class)
            ->name('create');

        Route::get('/{solicitud}', SolicitudesShow::class)
            ->name('show');

        Route::get('/{solicitud}/editar', SolicitudesEdit::class)
            ->name('edit');
    });
