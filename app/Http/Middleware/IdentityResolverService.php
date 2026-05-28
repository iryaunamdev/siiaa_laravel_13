<?php

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidIdentity
{
    public function handle(Request $request, Closure $next): Response
    {
        $identity = session('active_identity');

        if (! $request->user() || ! $identity) {
            abort(403, 'No se encontró una identidad institucional válida para este usuario.');
        }

        return $next($request);
    }
}
