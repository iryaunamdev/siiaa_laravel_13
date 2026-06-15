<?php

namespace App\Services\Notifications;

use App\Models\Solicitudes\Solicitud;

interface NotificationServiceInterface
{
    /**
     * Notifica al solicitante y al Consejo Interno que una solicitud fue enviada.
     */
    public function solicitudEnviada(Solicitud $solicitud): void;

    /**
     * Notifica al solicitante que su solicitud fue aprobada.
     */
    public function solicitudAprobada(Solicitud $solicitud): void;

    /**
     * Notifica al solicitante que su solicitud fue rechazada.
     */
    public function solicitudRechazada(Solicitud $solicitud): void;

    /**
     * Notifica al solicitante que su solicitud fue cerrada.
     */
    public function solicitudCerrada(Solicitud $solicitud): void;
}