<?php

use App\Livewire\Sys\Users\Index as UsersIndex;

use Illuminate\Support\Facades\Route;
use App\Livewire\Sys\Documentacion\Index as DocumentacionIndex;
use App\Livewire\Sys\RolesPermissions\Index as RolesPermissionsIndex;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'can:dashboard.view'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'can:sys.users.view'])->group(function () {
    // Primer componente real del módulo de usuarios.
    Route::get('sys/users', UsersIndex::class)->name('sys.users.index');
});

Route::get('/sys/roles-permisos', RolesPermissionsIndex::class)
    ->middleware(['auth', 'role:super-admin'])
    ->name('sys.roles-permisos.index');

Route::middleware(['auth', 'verified'])
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        Route::get('/documentacion', DocumentacionIndex::class)
            ->name('documentacion.index');
    });


require __DIR__ . '/settings.php';