<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Models\ConsejoInterno\CiActa;
use App\Models\ConsejoInterno\CiPunto;
use App\Models\ConsejoInterno\CiReunion;
use App\Policies\ConsejoInterno\CiActaPolicy;
use App\Policies\ConsejoInterno\CiPuntoPolicy;
use App\Policies\ConsejoInterno\CiReunionPolicy;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        CiReunion::class => CiReunionPolicy::class,
        CiPunto::class   => CiPuntoPolicy::class,
        CiActa::class    => CiActaPolicy::class,
    ];
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
        $this->configureDefaults();

        /**
         * Bypass global para super-admin.
         *
         * Permite acceso total sin validar permisos individuales.
         * Fundamental para administración del sistema.
         */
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }
}