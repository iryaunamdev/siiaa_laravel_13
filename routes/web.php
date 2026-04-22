<?php

use App\Livewire\Sys\Users\Index as UsersIndex;
use App\Livewire\Sys\Users\Create as UsersCreate;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'can:dashboard.view'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'can:sys.users.view'])->group(function () {
    // Primer componente real del módulo de usuarios.
    Route::get('sys/users', UsersIndex::class)->name('sys.users.index');
    Route::get('sys/users/create', UsersCreate::class)
        ->middleware('can:sys.users.create')
        ->name('sys.users.create');
});


require __DIR__ . '/settings.php';