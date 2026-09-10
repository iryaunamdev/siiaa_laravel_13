<?php

namespace App\Jobs\ConsejoInterno;

use App\Mail\ConsejoInterno\ResolucionSolicitudMail;
use App\Models\ConsejoInterno\CiNotificacion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnviarNotificacionCiJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public int $notificacionId
    ) {
    }

    public function backoff(): array
    {
        return [
            60,
            300,
            900,
        ];
    }

    public function handle(): void
    {
        $notificacion = CiNotificacion::query()
            ->findOrFail($this->notificacionId);

        /*
         * Idempotencia.
         *
         * Si por alguna razón el mismo job entra nuevamente después de un
         * envío exitoso, no volvemos a enviar.
         */
        if (
            $notificacion->estatus
            === CiNotificacion::ESTATUS_ENVIADA
        ) {
            return;
        }

        $notificacion->increment('intentos');

        try {
            Mail::to(
                $notificacion->destinatario_email,
                $notificacion->destinatario_nombre
            )->send(
                new ResolucionSolicitudMail($notificacion)
            );

            $notificacion->forceFill([
                'estatus' => CiNotificacion::ESTATUS_ENVIADA,
                'sent_at' => now(),
                'failed_at' => null,
                'error' => null,
            ])->save();
        } catch (Throwable $exception) {
            /*
             * Guardamos el último error, pero dejamos que Laravel reintente.
             */
            $notificacion->forceFill([
                'error' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        CiNotificacion::query()
            ->whereKey($this->notificacionId)
            ->update([
                'estatus' => CiNotificacion::ESTATUS_FALLIDA,
                'failed_at' => now(),
                'error' => $exception?->getMessage(),
            ]);
    }
}
