<?php

namespace App\Services\ConsejoInterno;

use App\Models\ConsejoInterno\CiNotificacion;
use App\Models\ConsejoInterno\CiPunto;

interface CiNotificacionServiceInterface
{
    public function registrarResolucionSolicitud(
        CiPunto $punto,
        ?int $identityId
    ): ?CiNotificacion;

    public function reenviar(
        CiNotificacion $notificacion,
        ?int $identityId
    ): CiNotificacion;
}
