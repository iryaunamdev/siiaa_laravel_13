<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Services\Auth\AccessLogger;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();

        // Punto central de autenticación local.
        // Más adelante este bloque podrá extenderse para resolver
        // autenticación LDAP y/o segundo factor sin romper la bitácora.
        Fortify::authenticateUsing(function (Request $request) {
            $accessLogger = app(AccessLogger::class);

            // En esta fase el acceso se resuelve por username.
            // Se conserva como base local para luego convivir con LDAP.
            $user = User::where('username', $request->username)->first();

            if (
                $user &&
                $user->is_active &&
                $user->password &&
                Hash::check($request->password, $user->password)
            ) {
                // Se actualiza el último acceso exitoso directamente
                // en el usuario para soporte y consulta rápida.
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);

                // Se registra el acceso exitoso en la bitácora histórica.
                $accessLogger->log(
                    request: $request,
                    event: 'login_success',
                    user: $user,
                    authType: $user->auth_type,
                    context: [
                        'username' => $request->username,
                    ]
                );

                return $user;
            }

            // Se registra también el intento fallido, incluso si el usuario
            // no existe, para mantener trazabilidad completa de accesos.
            $accessLogger->log(
                request: $request,
                event: 'login_failed',
                user: $user,
                authType: $user?->auth_type ?? 'local',
                context: [
                    'username' => $request->username,
                    'reason' => 'Credenciales inválidas o usuario inactivo',
                ]
            );

            return null;
        });
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn() => view('livewire.auth.login'));
        Fortify::verifyEmailView(fn() => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn() => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn() => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn() => view('livewire.auth.register'));
        Fortify::resetPasswordView(fn() => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}