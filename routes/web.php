<?php

use App\Livewire\Sys\Users\Index as UsersIndex;
use App\Livewire\Sys\Users\Create as UsersCreate;
use App\Livewire\Sys\Users\Edit as UsersEdit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Sys\Documentacion\Index as DocumentacionIndex;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'can:dashboard.view'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'can:sys.users.view'])->group(function () {
    // Primer componente real del módulo de usuarios.
    Route::get('sys/users', UsersIndex::class)->name('sys.users.index');
});

Route::middleware(['auth', 'verified'])
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        Route::get('/documentacion', DocumentacionIndex::class)
            ->name('documentacion.index');
    });


require __DIR__ . '/settings.php';