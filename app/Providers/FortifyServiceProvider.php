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
use App\Services\Auth\LdapService;

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
            $ldap = app(LdapService::class);
            $ldapLogging = $ldap->isLoggingEnabled();

            $username = $request->username;
            $password = $request->password;

            $user = User::where('username', $username)->first();

            /*
    |--------------------------------------------------------------------------
    | 1. Usuario existente en BD
    |--------------------------------------------------------------------------
    */
            if ($user) {
                if (! $user->is_active) {
                    $accessLogger->log(
                        request: $request,
                        event: 'login_failed',
                        user: $user,
                        authType: $user->auth_type,
                        context: [
                            'username' => $username,
                            'reason' => 'Usuario inactivo',
                        ]
                    );

                    return null;
                }

                /*
        |--------------------------------------------------------------------------
        | Usuario LDAP existente
        |--------------------------------------------------------------------------
        */
                if ($user->auth_type === 'ldap') {
                    if (! $ldap->isEnabled()) {
                        if ($ldapLogging) {
                            $accessLogger->log(
                                request: $request,
                                event: 'login_failed',
                                user: $user,
                                authType: 'ldap',
                                context: [
                                    'username' => $username,
                                    'reason' => 'LDAP desactivado',
                                ]
                            );
                        }
                        return null;
                    }

                    $ldapUser = $ldap->authenticate($username, $password);

                    if (! $ldapUser) {
                        if ($ldapLogging) {
                            $accessLogger->log(
                                request: $request,
                                event: 'login_failed',
                                user: $user,
                                authType: 'ldap',
                                context: [
                                    'username' => $username,
                                    'reason' => 'Credenciales LDAP inválidas',
                                ]
                            );
                        }
                        return null;
                    }

                    $syncData = array_filter([
                        'name' => $ldapUser['name'] ?? null,
                        'email' => $ldapUser['email'] ?? null,
                        'ldap_uid' => $ldapUser['username'],
                        'ldap_dn' => $ldapUser['dn'] ?? null,
                    ], fn($value) => ! is_null($value));

                    $changedData = [];

                    foreach ($syncData as $field => $value) {
                        if ($user->{$field} !== $value) {
                            $changedData[$field] = $value;
                        }
                    }

                    $user->update([
                        ...$changedData,
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip(),
                    ]);

                    $accessLogger->log(
                        request: $request,
                        event: 'login_success',
                        user: $user,
                        authType: 'ldap',
                        context: [
                            'username' => $username,
                            'synced_fields' => array_keys($changedData),
                        ]
                    );

                    if ($user->requiresTwoFactor()) {
                        if (empty($user->two_factor_secret)) {
                            session(['2fa:setup_required' => true]);
                        }
                    }

                    return $user;
                }

                /*
        |--------------------------------------------------------------------------
        | Usuario local existente
        |--------------------------------------------------------------------------
        */
                if (
                    $user->auth_type === 'local' &&
                    $user->password &&
                    Hash::check($password, $user->password)
                ) {
                    $user->update([
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip(),
                    ]);

                    $accessLogger->log(
                        request: $request,
                        event: 'login_success',
                        user: $user,
                        authType: 'local',
                        context: [
                            'username' => $username,
                        ]
                    );

                    return $user;
                }

                $accessLogger->log(
                    request: $request,
                    event: 'login_failed',
                    user: $user,
                    authType: $user->auth_type,
                    context: [
                        'username' => $username,
                        'reason' => 'Credenciales inválidas',
                    ]
                );

                return null;
            }

            /*
    |--------------------------------------------------------------------------
    | 2. Usuario no existe en BD: autoregistro LDAP
    |--------------------------------------------------------------------------
    */
            if ($ldap->isEnabled()) {
                $ldapUser = $ldap->authenticate($username, $password);

                if ($ldapUser) {
                    $user = User::create([
                        'username' => $ldapUser['username'],
                        'name' => $ldapUser['name'],
                        'email' => $ldapUser['email'],
                        'auth_type' => 'ldap',
                        'ldap_uid' => $ldapUser['username'],
                        'ldap_dn' => $ldapUser['dn'],
                        'password' => null,
                        'is_active' => true,
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip(),
                    ]);

                    /*
             * Aquí después conectamos la asignación automática de rol:
             * - investigador
             * - academico
             * - administrativo
             * - estudiante
             * - usuario
             *
             * Con base en tabla de personal / estudiantes.
             */

                    $accessLogger->log(
                        request: $request,
                        event: 'login_success',
                        user: $user,
                        authType: 'ldap',
                        context: [
                            'username' => $username,
                            'reason' => 'Autoregistro LDAP',
                        ]
                    );

                    return $user;
                }
            }

            /*
    |--------------------------------------------------------------------------
    | 3. Fallo general
    |--------------------------------------------------------------------------
    */
            $accessLogger->log(
                request: $request,
                event: 'login_failed',
                user: null,
                authType: $ldap->isEnabled() ? 'ldap' : 'local',
                context: [
                    'username' => $username,
                    'reason' => 'Usuario no encontrado o credenciales inválidas',
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