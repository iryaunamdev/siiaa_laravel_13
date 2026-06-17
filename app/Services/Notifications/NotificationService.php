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

    public function solicitudAprobada(Solicitud $solicitud): void
    {
        $this->mailService->enviarSolicitudAprobadaSolicitante($solicitud);
    }

    public function solicitudRechazada(Solicitud $solicitud): void
    {
        $this->mailService->enviarSolicitudRechazadaSolicitante($solicitud);
    }

    public function solicitudCerrada(Solicitud $solicitud): void
    {
        $this->mailService->enviarSolicitudCerradaSolicitante($solicitud);
    }
}
