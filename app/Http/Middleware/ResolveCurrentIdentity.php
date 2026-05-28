<?php

namespace App\Http\Middleware;

use App\Services\Identity\IdentityResolverService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCurrentIdentity
{
    public function __construct(
        protected IdentityResolverService $identityResolver,
    ) {}

    /**
     * Resuelve la identidad institucional activa del usuario autenticado.
     *
     * Este middleware no sustituye la autenticación. Solo se ejecuta
     * cuando ya existe un usuario autenticado en la sesión.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Evitar reprocesamiento innecesario
        |--------------------------------------------------------------------------
        |
        | Si ya existe identidad activa en sesión, no se vuelve a resolver
        | en cada request. La identidad se mantiene hasta logout, cambio de
        | sesión o suplantación.
        |
        */

        if (
            session()->has('current_identity_id') &&
            session()->has('current_identity_type')
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolver identidad bajo demanda
        |--------------------------------------------------------------------------
        |
        | Si el usuario coincide con una persona SIIAA o con un estudiante
        | SIIAP activo, aquí se crea/actualiza identity_links y se guarda la
        | identidad en sesión.
        |
        */

        $this->identityResolver->resolveForUser($user);

        return $next($request);
    }
}
