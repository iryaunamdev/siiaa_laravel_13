<?php

namespace App\Services\Solicitudes;

use App\Models\Solicitudes\Solicitud;
use App\Models\Solicitudes\SolicitudDocumento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface SolicitudServiceInterface
{
    public function crearBorrador(array $data, int $ownerId, ?int $actorIdentityId = null): Solicitud;

    public function actualizar(Solicitud $solicitud, array $data, ?int $actorIdentityId): Solicitud;

    public function enviar(Solicitud $solicitud, ?int $actorIdentityId): Solicitud;
    public function cancelar(Solicitud $solicitud, int $actorIdentityId, ?string $motivo = null): Solicitud;

    public function eliminar(Solicitud $solicitud, ?int $actorIdentityId): void;

    public function aprobarCi(Solicitud $solicitud, int $actorIdentityId, ?string $observaciones = null): Solicitud;

    public function rechazarCi(Solicitud $solicitud, int $actorIdentityId, string $motivo): Solicitud;

    public function pasarATramitePago(Solicitud $solicitud, int $actorIdentityId): Solicitud;

    public function marcarPagada(Solicitud $solicitud, int $actorIdentityId): Solicitud;

    public function cerrar(Solicitud $solicitud, int $actorIdentityId, ?string $observaciones = null): Solicitud;

    public function archivar(Solicitud $solicitud, int $actorIdentityId, string $motivo): Solicitud;

    public function guardarVisitante(Solicitud $solicitud, array $data, ?int $actorIdentityId): Solicitud;

    public function sincronizarRecursos(Solicitud $solicitud, array $recursos, ?int $actorIdentityId): Solicitud;

    /**
     * Los requerimientos pertenecen al expediente de solicitud,
     * no al visitante.
     *
     * @param array<int> $requerimientoIds
     */
    public function sincronizarRequerimientos(Solicitud $solicitud, array $requerimientoIds, ?int $actorIdentityId): Solicitud;

    public function adjuntarDocumento(Solicitud $solicitud, UploadedFile $file, ?int $actorIdentityId): SolicitudDocumento;

    public function eliminarDocumento(SolicitudDocumento $documento, ?int $actorIdentityId): void;

    public function listarParaIdentity(int $identityId, array $filters = []): Collection;
}
