<?php

namespace App\Services\Mail;

use App\Models\Solicitudes\Solicitud;

interface MailServiceInterface
{
    public function enviarSolicitudEnviadaSolicitante(Solicitud $solicitud): void;

    public function enviarSolicitudEnviadaConsejoInterno(Solicitud $solicitud): void;

    public function enviarSolicitudAprobadaSolicitante(Solicitud $solicitud): void;

    public function enviarSolicitudRechazadaSolicitante(Solicitud $solicitud): void;

    public function enviarSolicitudCerradaSolicitante(Solicitud $solicitud): void;
}