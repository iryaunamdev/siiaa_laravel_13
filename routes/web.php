<?php

use App\Livewire\Sys\Users\Index as UsersIndex;

use Illuminate\Support\Facades\Route;
use App\Livewire\Sys\Documentacion\Index as DocumentacionIndex;
use App\Livewire\Sys\RolesPermissions\Index as RolesPermissionsIndex;
use App\Livewire\Sys\PermissionsMatrix;
use App\Livewire\Sys\Catalogos\Index as CatalogosIndex;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'can:dashboard.view'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified'])
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        Route::get('/users', UsersIndex::class)
            ->middleware('can:users.view')
            ->name('users.index');
        Route::get('/roles-permisos', RolesPermissionsIndex::class)
            ->middleware(['auth', 'role:super-admin'])
            ->name('roles-permisos.index');
        Route::get('/documentacion', DocumentacionIndex::class)
            ->name('documentacion.index');
        Route::get('/matriz-permisos', PermissionsMatrix::class)
            ->middleware('can:matrix.view')
            ->name('permissions-matrix');
        Route::get('/catalogos', CatalogosIndex::class)
            ->middleware('can:catalogos.view')
            ->name('catalogos');
    });

require __DIR__ . '/settings.php';
