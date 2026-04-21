<?php

namespace App\Listeners;

use App\Services\Auth\AccessLogger;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Registra el cierre de sesión del usuario autenticado.
     *
     * Este evento complementa la bitácora de accesos y permite
     * reconstruir el ciclo básico de sesión: login → uso → logout.
     */
    public function handle(Logout $event): void
    {
        if (! request()) {
            return;
        }

        app(AccessLogger::class)->log(
            request: request(),
            event: 'logout',
            user: $event->user,
            authType: $event->user?->auth_type,
            context: [
                'username' => $event->user?->username,
            ]
        );
    }
}