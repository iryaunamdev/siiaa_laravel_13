<?php

namespace App\Services\ConsejoInterno;

use App\Models\ConsejoInterno\CiDocumento;
use App\Models\ConsejoInterno\CiPunto;
use App\Models\ConsejoInterno\CiPuntoEvaluacion;
use App\Models\ConsejoInterno\CiReunion;
use App\Models\ConsejoInterno\CiReunionParticipante;
use App\Models\Solicitudes\Solicitud;
use App\Models\Catalogo;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use App\Support\Solicitudes\SolicitudCatalogos;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CiReunionService implements CiReunionServiceInterface
{
    public function __construct(
        private readonly CiNotificacionServiceInterface $notificacionService,
    ) {}

    public function crear(array $data, ?int $identityId): CiReunion
    {
        return DB::transaction(function () use ($data, $identityId) {
            $payload = $this->normalizarDatosBase($data);
            $payload['created_by'] = $identityId;
            $payload['updated_by'] = $identityId;

            return CiReunion::query()->create($payload);
        });
    }

    public function actualizar(CiReunion $reunion, array $data, ?int $identityId): CiReunion
    {
        return DB::transaction(function () use ($reunion, $data, $identityId) {
            $payload = $this->normalizarDatosBase($data);

            $reunion->fill($payload);

            if ($reunion->isDirty()) {
                $reunion->updated_by = $identityId;
                $reunion->save();
            }

            return $reunion->refresh();
        });
    }

    public function eliminar(CiReunion $reunion): void
    {
        DB::transaction(function () use ($reunion) {
            $reunion->delete();
        });
    }

    private function normalizarDatosBase(array $data): array
    {
        $payload = Arr::only($data, [
            'titulo',
            'fecha',
            'tipo_reunion',
            'modalidad',
            'estatus',
        ]);

        $payload['titulo'] = trim((string) ($payload['titulo'] ?? ''));

        $payload['tipo_reunion'] = trim((string) (
            $payload['tipo_reunion'] ?? ConsejoInternoCatalogos::TIPO_REUNION_DEFAULT
        ));

        $payload['modalidad'] = trim((string) (
            $payload['modalidad'] ?? ConsejoInternoCatalogos::MODALIDAD_DEFAULT
        ));

        $payload['estatus'] = trim((string) (
            $payload['estatus'] ?? ConsejoInternoCatalogos::REUNION_EN_PROCESO
        ));

        return $payload;
    }

    public function agregarParticipante(CiReunion $reunion, int $identityLinkId): void
    {
        DB::transaction(function () use ($reunion, $identityLinkId) {
            CiReunionParticipante::query()->firstOrCreate([
                'reunion_id' => $reunion->id,
                'identity_link_id' => $identityLinkId,
            ]);
        });
    }

    public function quitarParticipante(CiReunion $reunion, int $identityLinkId): void
    {
        DB::transaction(function () use ($reunion, $identityLinkId) {
            CiReunionParticipante::query()
                ->where('reunion_id', $reunion->id)
                ->where('identity_link_id', $identityLinkId)
                ->delete();
        });
    }

    /* Solicitudes */
    public function agregarSolicitud(CiReunion $reunion, int $solicitudId): void
    {
        DB::transaction(function () use ($reunion, $solicitudId) {
            $solicitud = Solicitud::query()
                ->with('estatus')
                ->findOrFail($solicitudId);

            if (!$solicitud->estaEnviada()) {
                throw new \InvalidArgumentException(
                    'Solo se pueden agregar solicitudes enviadas al Consejo Interno.'
                );
            }

            $tipoPuntoId = $this->tipoPuntoSolicitudId();

            CiPunto::query()->firstOrCreate(
                [
                    'reunion_id' => $reunion->id,
                    'solicitud_id' => $solicitud->id,
                ],
                [
                    'tipo_punto_id' => $tipoPuntoId,
                    'orden' => $this->siguienteOrdenSolicitud($reunion),
                ]
            );
        });
    }

    public function quitarSolicitud(CiReunion $reunion, int $solicitudId): void
    {
        DB::transaction(function () use ($reunion, $solicitudId) {
            CiPunto::query()
                ->where('reunion_id', $reunion->id)
                ->where('solicitud_id', $solicitudId)
                ->where('tipo_punto_id', $this->tipoPuntoSolicitudId())
                ->delete();
        });
    }

    private function tipoPuntoSolicitudId(): int
    {
        return Catalogo::query()
            ->byClave(ConsejoInternoCatalogos::CATALOGO_TIPOS_PUNTO)
            ->firstOrFail()
            ->itemsActivos()
            ->where('clave', ConsejoInternoCatalogos::TIPO_PUNTO_SOLICITUD)
            ->value('id');
    }

    private function siguienteOrdenSolicitud(CiReunion $reunion): int
    {
        return ((int) CiPunto::query()
            ->where('reunion_id', $reunion->id)
            ->where('tipo_punto_id', $this->tipoPuntoSolicitudId())
            ->max('orden')) + 1;
    }

    /* Otros puntos */
    public function agregarOtroPunto(CiReunion $reunion, array $data): void
    {
        DB::transaction(function () use ($reunion, $data) {
            $payload = Arr::only($data, [
                'titulo',
                'descripcion',
            ]);

            $payload['titulo'] = trim((string) ($payload['titulo'] ?? ''));
            $payload['descripcion'] = trim((string) ($payload['descripcion'] ?? ''));

            CiPunto::query()->create([
                'reunion_id' => $reunion->id,
                'tipo_punto_id' => $this->tipoPuntoOtroId(),
                'solicitud_id' => null,
                'titulo' => $payload['titulo'],
                'descripcion' => $payload['descripcion'],
                'orden' => $this->siguienteOrdenOtroPunto($reunion),
            ]);
        });
    }

    public function quitarOtroPunto(CiReunion $reunion, int $puntoId): void
    {
        DB::transaction(function () use ($reunion, $puntoId) {
            CiPunto::query()
                ->where('reunion_id', $reunion->id)
                ->where('id', $puntoId)
                ->where('tipo_punto_id', $this->tipoPuntoOtroId())
                ->delete();
        });
    }

    private function tipoPuntoOtroId(): int
    {
        return Catalogo::query()
            ->byClave(ConsejoInternoCatalogos::CATALOGO_TIPOS_PUNTO)
            ->firstOrFail()
            ->itemsActivos()
            ->where('clave', ConsejoInternoCatalogos::TIPO_PUNTO_OTRO)
            ->value('id');
    }

    private function siguienteOrdenOtroPunto(CiReunion $reunion): int
    {
        return ((int) CiPunto::query()
            ->where('reunion_id', $reunion->id)
            ->where('tipo_punto_id', $this->tipoPuntoOtroId())
            ->max('orden')) + 1;
    }

    /* Documentos */
    public function subirDocumento(CiReunion $reunion, UploadedFile $file, ?int $identityId): void
    {
        DB::transaction(function () use ($reunion, $file, $identityId) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            $filename = Str::uuid()->toString()
                . ($extension ? ".{$extension}" : '');

            $path = $file->storeAs(
                "consejo-interno/reuniones/{$reunion->id}",
                $filename
            );

            CiDocumento::query()->create([
                'reunion_id' => $reunion->id,
                'filename' => $filename,
                'original_name' => $originalName,
                'path' => $path,
                'mime_type' => $mimeType,
                'size' => $size,
                'uploaded_by' => $identityId,
            ]);
        });
    }

    public function eliminarDocumento(CiReunion $reunion, int $documentoId): void
    {
        DB::transaction(function () use ($reunion, $documentoId) {
            $documento = CiDocumento::query()
                ->where('reunion_id', $reunion->id)
                ->findOrFail($documentoId);

            if (filled($documento->path)) {
                Storage::delete($documento->path);
            }

            $documento->delete();
        });
    }

    public function evaluarPunto(
        CiPunto $punto,
        ?int $identityLinkId,
        string $evaluacionClave,
        ?string $comentarios = null
    ): void {
        DB::transaction(function () use ($punto, $identityLinkId, $evaluacionClave, $comentarios) {
            $evaluacionId = $this->evaluacionIdPorClave($evaluacionClave);

            CiPuntoEvaluacion::query()->updateOrCreate(
                [
                    'punto_id' => $punto->id,
                    'identity_link_id' => $identityLinkId,
                ],
                [
                    'evaluacion_id' => $evaluacionId,
                    'comentarios' => filled($comentarios)
                        ? trim((string) $comentarios)
                        : null,
                ]
            );
        });
    }

    private function evaluacionIdPorClave(string $clave): int
    {
        return Catalogo::query()
            ->byClave(ConsejoInternoCatalogos::CATALOGO_EVALUACIONES)
            ->firstOrFail()
            ->itemsActivos()
            ->where('clave', $clave)
            ->value('id');
    }

    public function resolverPunto(
        CiPunto $punto,
        string $resolucion,
        ?int $identityId
    ): void {
        DB::transaction(function () use ($punto, $resolucion, $identityId) {
            if (! in_array(
                $resolucion,
                ConsejoInternoCatalogos::resolucionesFinales(),
                true
            )) {
                throw new \InvalidArgumentException(
                    'La resolución indicada no es válida.'
                );
            }

            $punto->forceFill([
                'resolucion' => $resolucion,
                'resolved_at' => now(),
                'resolved_by' => $identityId,
            ])->save();

            $punto->loadMissing([
                'tipoPunto',
                'reunion.documentos',
                'solicitud.owner',
                'solicitud.tipoSolicitud',
                'solicitud.estatus',
            ]);

            if (! $punto->esSolicitud() || ! $punto->solicitud) {
                return;
            }

            $debeNotificar = match ($resolucion) {
                ConsejoInternoCatalogos::RESOLUCION_ACEPTAR =>
                $this->aprobarSolicitudPorConsejoInterno(
                    $punto->solicitud,
                    $identityId
                ),

                ConsejoInternoCatalogos::RESOLUCION_RECHAZAR =>
                $this->rechazarSolicitudPorConsejoInterno(
                    $punto->solicitud,
                    $identityId
                ),

                ConsejoInternoCatalogos::RESOLUCION_DISCUTIR => false,

                default => false,
            };

            if (! $debeNotificar) {
                return;
            }

            $this->notificacionService->registrarResolucionSolicitud(
                $punto->fresh([
                    'tipoPunto',
                    'reunion.documentos',
                    'solicitud.owner',
                    'solicitud.tipoSolicitud',
                    'solicitud.estatus',
                ]),
                $identityId
            );
        });
    }

    // Metodos privados
    private function aprobarSolicitudPorConsejoInterno(
        Solicitud $solicitud,
        ?int $identityId
    ): bool {
        if (! $solicitud->estaEnviada()) {
            return false;
        }

        $payload = [
            'estatus_id' => $this->solicitudEstatusIdPorClave(
                SolicitudCatalogos::ESTATUS_APROBADA_CI
            ),
            'approved_at' => now(),
            'approved_by' => $identityId,
        ];

        if (! $solicitud->requiere_recursos) {
            $payload['estatus_id'] = $this->solicitudEstatusIdPorClave(
                SolicitudCatalogos::ESTATUS_CERRADA
            );

            $payload['closed_at'] = now();
            $payload['closed_by'] = $identityId;
        }

        $solicitud->forceFill($payload)->save();

        return true;
    }

    private function rechazarSolicitudPorConsejoInterno(
        Solicitud $solicitud,
        ?int $identityId
    ): bool {
        if (! $solicitud->estaEnviada()) {
            return false;
        }

        $solicitud->forceFill([
            'estatus_id' => $this->solicitudEstatusIdPorClave(
                SolicitudCatalogos::ESTATUS_CERRADA
            ),
            'rejected_at' => now(),
            'rejected_by' => $identityId,
            'closed_at' => now(),
            'closed_by' => $identityId,
        ])->save();

        return true;
    }

    private function solicitudEstatusIdPorClave(string $clave): int
    {
        return Catalogo::query()
            ->byClave(SolicitudCatalogos::CATALOGO_ESTATUS)
            ->firstOrFail()
            ->itemsActivos()
            ->where('clave', $clave)
            ->value('id');
    }

    public function concluir(
        CiReunion $reunion,
        ?int $identityId
    ): void {
        DB::transaction(function () use ($reunion, $identityId) {
            if ($reunion->estaConcluida()) {
                return;
            }

            $reunion->forceFill([
                'estatus' => ConsejoInternoCatalogos::REUNION_CONCLUIDA,
                'concluida_at' => now(),
                'concluida_by' => $identityId,
                'updated_by' => $identityId,
            ])->save();
        });
    }
}
