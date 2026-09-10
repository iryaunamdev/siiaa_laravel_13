<?php

namespace App\Providers;

use App\Services\Mail\MailService;
use App\Services\Mail\MailServiceInterface;
use App\Services\Notifications\NotificationService;
use App\Services\Notifications\NotificationServiceInterface;
use App\Services\Solicitudes\SolicitudService;
use App\Services\Solicitudes\SolicitudServiceInterface;
use App\Services\ConsejoInterno\CiReunionService;
use App\Services\ConsejoInterno\CiReunionServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceBindingsProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            SolicitudServiceInterface::class,
            SolicitudService::class
        );

        $this->app->singleton(
            NotificationServiceInterface::class,
            NotificationService::class
        );

        $this->app->singleton(
            MailServiceInterface::class,
            MailService::class
        );

        $this->app->singleton(
            CiReunionServiceInterface::class,
            CiReunionService::class
        );
    }
}
