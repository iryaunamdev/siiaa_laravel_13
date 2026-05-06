<?php

use App\Livewire\Sys\Users\Index as UsersIndex;

use Illuminate\Support\Facades\Route;
use App\Livewire\Sys\Documentacion\Index as DocumentacionIndex;
use App\Livewire\Sys\RolesPermissions\Index as RolesPermissionsIndex;
use App\Livewire\Sys\PermissionsMatrix;
use App\Livewire\Sys\Catalogos\Index as CatalogosIndex;
use App\Livewire\Sys\Config\AuthSettings;
use App\Livewire\Sys\Users\Security;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', '2fa.configured', 'permission:dashboard.view'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', '2fa.configured',])
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        Route::get('/users', UsersIndex::class)
            ->middleware('permission:users.view')
            ->name('users.index');
        Route::get('/roles-permisos', RolesPermissionsIndex::class)
            ->middleware(['auth', 'role:super-admin'])
            ->name('roles-permisos.index');
        Route::get('/documentacion', DocumentacionIndex::class)
            ->name('documentacion.index');
        Route::get('/matriz-permisos', PermissionsMatrix::class)
            ->middleware('permission:matrix.view')
            ->name('permissions-matrix');
        Route::get('/catalogos', CatalogosIndex::class)
            ->middleware('permission:catalogos.view')
            ->name('catalogos');
        Route::get('/configuracion', AuthSettings::class)
            ->middleware('permission:sys.settings')
            ->name('settings');
    });

Route::middleware(['auth', 'verified'])
    ->get('/user/security', Security::class)
    ->name('user.security');

require __DIR__ . '/settings.php';
