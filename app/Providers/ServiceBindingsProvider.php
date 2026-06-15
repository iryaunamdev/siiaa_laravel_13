<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Mail\MailService;
use App\Services\Mail\MailServiceInterface;

use App\Services\Notifications\NotificationService;
use App\Services\Notifications\NotificationServiceInterface;

use App\Services\Solicitudes\SolicitudService;
use App\Services\Solicitudes\SolicitudServiceInterface;

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
    }
}