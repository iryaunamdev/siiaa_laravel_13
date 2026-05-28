<?php

namespace App\Providers;

use App\Listeners\ClearCurrentIdentityOnLogout;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\ResolveUserIdentityOnLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Registro de eventos del sistema.
     *
     * En esta fase solo se enlaza el evento de logout para completar
     * la trazabilidad básica del flujo de autenticación.
     */
    protected $listen = [
        Login::class => [
            ResolveUserIdentityOnLogin::class,
        ],
        Logout::class => [
            ClearCurrentIdentityOnLogout::class,
            LogSuccessfulLogout::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}