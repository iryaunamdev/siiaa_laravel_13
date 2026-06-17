<?php

namespace App\Services\Notifications;

use App\Models\Solicitudes\Solicitud;
use App\Services\Mail\MailServiceInterface;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        protected MailServiceInterface $mailService,
    ) {}

    public function solicitudEnviada(Solicitud $solicitud): void
    {
        $this->mailService->enviarSolicitudEnviadaSolicitante($solicitud);
        $this->mailService->enviarSolicitudEnviadaConsejoInterno($solicitud);
    }
}
