<?php

namespace App\Http\Middleware;

use App\Services\System\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsConfigured
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SettingService::class);

        $twoFactorEnabled = (bool) $settings->get('auth.2fa.enabled', false);

        if (! $twoFactorEnabled) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            return $next($request);
        }

        if ($request->routeIs([
            'user.security',
            'two-factor.*',
            'logout',
        ])) {
            return $next($request);
        }

        return redirect()
            ->route('user.security')
            ->with('warning', 'Para continuar, configura la autenticación en dos factores.');
    }
}
