<?php

use App\Livewire\Solicitudes\SolicitudesCreate;
use App\Livewire\Solicitudes\SolicitudesEdit;
use App\Livewire\Solicitudes\SolicitudesIndex;
use App\Livewire\Solicitudes\SolicitudesReview;
use App\Livewire\Solicitudes\SolicitudesShow;
use Illuminate\Support\Facades\SolicitudesRoute;

Route::middleware(['auth', 'verified'])
    ->prefix('solicitudes')
    ->name('solicitudes.')
    ->group(function () {
        Route::get('/', SolicitudesIndex::class)
            ->name('index');

        Route::get('/crear', SolicitudesCreate::class)
            ->name('create');

        Route::get('/{solicitud}', SolicitudesShow::class)
            ->name('show');

        Route::get('/{solicitud}/editar', SolicitudesEdit::class)
            ->name('edit');

        Route::get('/{solicitud}/revisar', SolicitudesReview::class)
            ->name('review');
    });