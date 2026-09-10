<?php

namespace App\Services\ConsejoInterno;

use App\Models\ConsejoInterno\CiPunto;
use App\Models\ConsejoInterno\CiReunion;
use Illuminate\Http\UploadedFile;

interface CiReunionServiceInterface
{
    public function crear(array $data, ?int $identityId): CiReunion;

    public function actualizar(CiReunion $reunion, array $data, ?int $identityId): CiReunion;

    public function eliminar(CiReunion $reunion): void;

    public function agregarParticipante(CiReunion $reunion, int $identityLinkId): void;

    public function quitarParticipante(CiReunion $reunion, int $identityLinkId): void;

    public function agregarSolicitud(CiReunion $reunion, int $solicitudId): void;

    public function quitarSolicitud(CiReunion $reunion, int $solicitudId): void;

    public function agregarOtroPunto(CiReunion $reunion, array $data): void;

    public function quitarOtroPunto(CiReunion $reunion, int $puntoId): void;

    public function subirDocumento(CiReunion $reunion, UploadedFile $file, ?int $identityId): void;

    public function eliminarDocumento(CiReunion $reunion, int $documentoId): void;

    public function evaluarPunto(
        CiPunto $punto,
        ?int $identityLinkId,
        string $evaluacionClave,
        ?string $comentarios = null
    ): void;

    public function resolverPunto(
        CiPunto $punto,
        string $resolucion,
        ?int $identityId
    ): void;

    public function concluir(
        CiReunion $reunion,
        ?int $identityId
    ): void;
}
