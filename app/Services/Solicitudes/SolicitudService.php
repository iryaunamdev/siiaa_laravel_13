<?php

namespace App\Services\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\Solicitudes\Solicitud;
use App\Models\Solicitudes\SolicitudDocumento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\Notifications\NotificationServiceInterface;
use LogicException;

class SolicitudService implements SolicitudServiceInterface
{
    public function __construct(
        protected NotificationServiceInterface $notificationService,
    ) {}

    public function crearBorrador(array $data, int $ownerId, ?int $actorIdentityId = null): Solicitud
    {
        $actorIdentityId = $actorIdentityId ?? $ownerId;
        unset($data['owner_id']);

        return DB::transaction(function () use ($data, $ownerId, $actorIdentityId) {
            return Solicitud::create(array_merge($data, [
                'owner_id' => $ownerId,
                'created_by' => $actorIdentityId,
                'updated_by' => $actorIdentityId,
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_BORRADOR),
            ]));
        });
    }

    public function actualizar(Solicitud $solicitud, array $data, int $actorIdentityId): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $data, $actorIdentityId) {
            if ($solicitud->perteneceA($actorIdentityId) && ! $solicitud->puedeEditarPropietario()) {
                throw new LogicException('La solicitud ya fue enviada y no puede editarse libremente.');
            }

            $solicitud->fill($data);
            $solicitud->updated_by = $actorIdentityId;
            $solicitud->save();

            return $solicitud->refresh();
        });
    }

    public function enviar(Solicitud $solicitud, int $actorIdentityId): Solicitud
    {
        $solicitud = DB::transaction(function () use ($solicitud, $actorIdentityId) {
            if (! $solicitud->perteneceA($actorIdentityId)) {
                throw new LogicException('Solo el propietario puede enviar esta solicitud.');
            }

            if (! $solicitud->puedeEnviar()) {
                throw new LogicException('La solicitud no se encuentra en estado borrador.');
            }

            $solicitud->forceFill([
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_ENVIADA),
                'submitted_at' => now(),
                'submitted_by' => $actorIdentityId,
                'updated_by' => $actorIdentityId,
            ])->save();

            return $solicitud->refresh();
        });

        $this->notificationService->solicitudEnviada($solicitud);

        return $solicitud;
    }

    public function cancelar(Solicitud $solicitud, int $actorIdentityId, ?string $motivo = null): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $actorIdentityId, $motivo) {
            if (! $solicitud->puedeCancelarPropietario() && ! $solicitud->perteneceA($actorIdentityId)) {
                throw new LogicException('La solicitud no puede cancelarse en su estado actual.');
            }

            $solicitud->forceFill([
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_CANCELADA),
                'cancelled_at' => now(),
                'cancelled_by' => $actorIdentityId,
                'cancel_reason' => $motivo,
                'updated_by' => $actorIdentityId,
            ])->save();

            return $solicitud->refresh();
        });
    }

    public function eliminar(Solicitud $solicitud, int $actorIdentityId): void
    {
        DB::transaction(function () use ($solicitud) {
            foreach ($solicitud->documentos as $documento) {
                if ($documento->path && Storage::disk('private')->exists($documento->path)) {
                    Storage::disk('private')->delete($documento->path);
                }
            }

            $solicitud->delete();
        });
    }

    public function aprobarCi(Solicitud $solicitud, int $actorIdentityId, ?string $observaciones = null): Solicitud
    {
        $solicitud = DB::transaction(function () use ($solicitud, $actorIdentityId, $observaciones) {
            if (! $solicitud->puedeRevisarse()) {
                throw new LogicException('La solicitud no se encuentra en estado revisable.');
            }

            $solicitud->forceFill([
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_APROBADA_CI),
                'approved_at' => now(),
                'approved_by' => $actorIdentityId,
                'observaciones_sacad' => $observaciones,
                'updated_by' => $actorIdentityId,
            ])->save();

            return $solicitud->refresh();
        });

        $this->notificationService->solicitudAprobada($solicitud);

        return $solicitud;
    }

    public function rechazarCi(Solicitud $solicitud, int $actorIdentityId, string $motivo): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $actorIdentityId, $motivo) {
            if (! $solicitud->puedeRevisarse()) {
                throw new LogicException('La solicitud no se encuentra en estado revisable.');
            }

            $solicitud->forceFill([
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_RECHAZADA_CI),
                'rejected_at' => now(),
                'rejected_by' => $actorIdentityId,
                'reject_reason' => $motivo,
                'updated_by' => $actorIdentityId,
            ])->save();

            $this->notificationService->solicitudRechazada($solicitud);

            return $solicitud->refresh();
        });
    }

    public function pasarATramitePago(Solicitud $solicitud, int $actorIdentityId): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $actorIdentityId) {
            if (! $solicitud->puedePasarATramitePago()) {
                throw new LogicException('La solicitud no puede pasar a tramite de pago.');
            }

            $solicitud->forceFill([
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_TRAMITE_PAGO),
                'updated_by' => $actorIdentityId,
            ])->save();

            return $solicitud->refresh();
        });
    }

    public function marcarPagada(Solicitud $solicitud, int $actorIdentityId): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $actorIdentityId) {
            if (! $solicitud->puedeMarcarsePagada()) {
                throw new LogicException('La solicitud no puede marcarse como pagada.');
            }

            $solicitud->forceFill([
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_PAGADA),
                'updated_by' => $actorIdentityId,
            ])->save();

            return $solicitud->refresh();
        });
    }

    public function cerrar(Solicitud $solicitud, int $actorIdentityId, ?string $observaciones = null): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $actorIdentityId, $observaciones) {
            if (! $solicitud->puedeCerrarse()) {
                throw new LogicException('La solicitud no puede cerrarse en su estado actual.');
            }

            $solicitud->forceFill([
                'estatus_id' => $this->estatusId(Solicitud::ESTATUS_CERRADA),
                'closed_at' => now(),
                'closed_by' => $actorIdentityId,
                'observaciones_administracion' => $observaciones,
                'updated_by' => $actorIdentityId,
            ])->save();

            $this->notificationService->solicitudRechazada($solicitud);

            return $solicitud->refresh();
        });
    }

    public function archivar(Solicitud $solicitud, int $actorIdentityId, string $motivo): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $actorIdentityId, $motivo) {
            if ($solicitud->estaArchivada()) {
                throw new LogicException('La solicitud ya se encuentra archivada.');
            }

            $solicitud->forceFill([
                'archived_at' => now(),
                'archived_by' => $actorIdentityId,
                'archive_reason' => $motivo,
                'updated_by' => $actorIdentityId,
            ])->save();

            return $solicitud->refresh();
        });
    }

    public function guardarVisitante(Solicitud $solicitud, array $data, int $actorIdentityId): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $data, $actorIdentityId) {
            $visitante = $solicitud->visitante;

            $solicitud->visitante()->updateOrCreate(
                ['solicitud_id' => $solicitud->id],
                array_merge($data, [
                    'created_by' => $visitante?->created_by ?? $actorIdentityId,
                    'updated_by' => $actorIdentityId,
                ])
            );

            return $solicitud->refresh();
        });
    }

    public function sincronizarRecursos(Solicitud $solicitud, array $recursos, int $actorIdentityId): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $recursos, $actorIdentityId) {
            $solicitud->recursos()->delete();

            foreach ($recursos as $recurso) {
                $solicitud->recursos()->create(array_merge($recurso, [
                    'created_by' => $actorIdentityId,
                    'updated_by' => $actorIdentityId,
                ]));
            }

            return $solicitud->refresh();
        });
    }

    public function sincronizarRequerimientos(Solicitud $solicitud, array $requerimientoIds, int $actorIdentityId): Solicitud
    {
        return DB::transaction(function () use ($solicitud, $requerimientoIds, $actorIdentityId) {
            $syncData = collect($requerimientoIds)
                ->filter()
                ->unique()
                ->mapWithKeys(fn($id) => [
                    (int) $id => [
                        'created_by' => $actorIdentityId,
                        'updated_by' => $actorIdentityId,
                    ],
                ])
                ->all();

            $solicitud->requerimientosCatalogo()->sync($syncData);

            return $solicitud->refresh();
        });
    }

    public function adjuntarDocumento(Solicitud $solicitud, UploadedFile $file, int $actorIdentityId): SolicitudDocumento
    {
        return DB::transaction(function () use ($solicitud, $file, $actorIdentityId) {
            $path = $file->store("solicitudes/{$solicitud->id}", 'private');

            return $solicitud->documentos()->create([
                'filename' => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $actorIdentityId,
            ]);
        });
    }

    public function eliminarDocumento(SolicitudDocumento $documento, int $actorIdentityId): void
    {
        DB::transaction(function () use ($documento) {
            if ($documento->path && Storage::disk('private')->exists($documento->path)) {
                Storage::disk('private')->delete($documento->path);
            }

            $documento->delete();
        });
    }

    public function listarParaIdentity(int $identityId, array $filters = []): Collection
    {
        return Solicitud::query()
            ->with([
                'tipoSolicitud',
                'estatus',
                'owner',
                'visitante',
            ])
            ->where('owner_id', $identityId)
            ->when($filters['estatus_id'] ?? null, fn($query, $estatusId) => $query->where('estatus_id', $estatusId))
            ->when($filters['tipo_solicitud_id'] ?? null, fn($query, $tipoId) => $query->where('tipo_solicitud_id', $tipoId))
            ->latest()
            ->get();
    }

    protected function estatusId(string $clave): int
    {
        return CatalogoItem::query()
            ->where('clave', $clave)
            ->whereHas('catalogo', fn($query) => $query->where('clave', 'SOL_EST'))
            ->value('id')
            ?? throw new LogicException("No existe el estatus de solicitud [{$clave}].");
    }
}
