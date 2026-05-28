<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockDuringImpersonation
{
    /**
     * Bloquea acciones sensibles mientras exista una suplantación activa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('impersonating_identity') === true) {
            abort(403, 'Esta acción no está disponible durante una suplantación de identidad.');
        }

        return $next($request);
    }
}