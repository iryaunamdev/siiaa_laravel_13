<?php

namespace App\Services\Notifications;

use App\Models\Solicitudes\Solicitud;

interface NotificationServiceInterface
{
    /**
     * Notifica al solicitante y al Consejo Interno que una solicitud fue enviada.
     *
     * El módulo Solicitudes termina funcionalmente en el envío formal (SENV).
     * Las revisiones y estados posteriores pertenecen al módulo Consejo Interno.
     */
    public function solicitudEnviada(Solicitud $solicitud): void;
}
