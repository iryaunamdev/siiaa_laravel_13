<?php

namespace App\Services\ConsejoInterno;

use App\Models\ConsejoInterno\CiActa;

interface CiActaServiceInterface
{
    public function crear(
        array $data,
        ?int $identityId
    ): CiActa;

    public function actualizar(
        CiActa $acta,
        array $data,
        ?int $identityId
    ): CiActa;

    public function eliminar(
        CiActa $acta
    ): void;

    public function publicar(
        CiActa $acta,
        ?int $identityId
    ): void;
}
