<?php

namespace App\Services\ConsejoInterno;

use App\Jobs\ConsejoInterno\EnviarNotificacionCiJob;
use App\Models\ConsejoInterno\CiNotificacion;
use App\Models\ConsejoInterno\CiPunto;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CiNotificacionService implements CiNotificacionServiceInterface
{
    public function registrarResolucionSolicitud(
        CiPunto $punto,
        ?int $identityId
    ): ?CiNotificacion {
        $punto->loadMissing([
            'tipoPunto',
            'reunion.documentos',
            'solicitud.owner',
            'solicitud.tipoSolicitud',
            'solicitud.estatus',
        ]);

        if (! $punto->esSolicitud() || ! $punto->solicitud) {
            return null;
        }

        if (! in_array($punto->resolucion, [
            ConsejoInternoCatalogos::RESOLUCION_ACEPTAR,
            ConsejoInternoCatalogos::RESOLUCION_RECHAZAR,
        ], true)) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Anti-duplicado automático
        |--------------------------------------------------------------------------
        |
        | Una resolución concreta de un punto solo genera automáticamente
        | una notificación.
        |
        | Los reenvíos posteriores son siempre manuales.
        |
        */

        $existente = CiNotificacion::query()
            ->where('punto_id', $punto->id)
            ->where('tipo', CiNotificacion::TIPO_RESOLUCION_SOLICITUD)
            ->where('resolucion', $punto->resolucion)
            ->whereNull('reenvio_de_id')
            ->first();

        if ($existente) {
            return $existente;
        }

        [$email, $nombre, $emailReal, $nombreReal, $esPrueba]
            = $this->resolverDestinatario($punto);

        $notificacion = CiNotificacion::query()->create([
            'reunion_id' => $punto->reunion_id,
            'punto_id' => $punto->id,
            'solicitud_id' => $punto->solicitud_id,

            'tipo' => CiNotificacion::TIPO_RESOLUCION_SOLICITUD,
            'resolucion' => $punto->resolucion,

            'destinatario_email' => $email,
            'destinatario_nombre' => $nombre,

            'destinatario_real_email' => $emailReal,
            'destinatario_real_nombre' => $nombreReal,

            'es_prueba' => $esPrueba,

            'asunto' => $this->asunto($punto),

            'payload' => $this->crearPayload($punto),
            'adjuntos' => $this->crearAdjuntos($punto),

            'estatus' => CiNotificacion::ESTATUS_PENDIENTE,
            'created_by' => $identityId,
        ]);

        $this->encolar($notificacion);

        return $notificacion;
    }

    public function reenviar(
        CiNotificacion $notificacion,
        ?int $identityId
    ): CiNotificacion {
        /*
         * Para el reenvío utilizamos nuevamente el destinatario real
         * y aplicamos las reglas del ambiente actual.
         */

        [
            $email,
            $nombre,
            $emailReal,
            $nombreReal,
            $esPrueba,
        ] = $this->resolverDestinatarioReal(
            $notificacion->destinatario_real_email,
            $notificacion->destinatario_real_nombre
        );

        $reenvio = CiNotificacion::query()->create([
            'reunion_id' => $notificacion->reunion_id,
            'punto_id' => $notificacion->punto_id,
            'solicitud_id' => $notificacion->solicitud_id,
            'reenvio_de_id' => $notificacion->id,

            'tipo' => $notificacion->tipo,
            'resolucion' => $notificacion->resolucion,

            'destinatario_email' => $email,
            'destinatario_nombre' => $nombre,

            'destinatario_real_email' => $emailReal,
            'destinatario_real_nombre' => $nombreReal,

            'es_prueba' => $esPrueba,

            'asunto' => $notificacion->asunto,

            /*
             * Reutilizamos exactamente el contenido histórico.
             */
            'payload' => $notificacion->payload,
            'adjuntos' => $notificacion->adjuntos,

            'estatus' => CiNotificacion::ESTATUS_PENDIENTE,

            'created_by' => $identityId,
        ]);

        $this->encolar($reenvio);

        return $reenvio;
    }

    private function encolar(CiNotificacion $notificacion): void
    {
        $notificacion->forceFill([
            'estatus' => CiNotificacion::ESTATUS_ENCOLADA,
            'queued_at' => now(),
            'failed_at' => null,
            'error' => null,
        ])->save();

        EnviarNotificacionCiJob::dispatch($notificacion->id)
            ->onQueue(config('consejo-interno.mail.queue'))
            ->afterCommit();
    }

    private function resolverDestinatario(CiPunto $punto): array
    {
        return $this->resolverDestinatarioReal(
            $punto->solicitud?->owner?->emailResolved(),
            $punto->solicitud?->owner?->fullname()
        );
    }

    private function resolverDestinatarioReal(
        ?string $emailReal,
        ?string $nombreReal
    ): array {
        /*
        |--------------------------------------------------------------------------
        | PRODUCTION
        |--------------------------------------------------------------------------
        */

        if (app()->environment('production')) {
            if (blank($emailReal)) {
                throw new RuntimeException(
                    'La solicitud no tiene un correo institucional disponible.'
                );
            }

            return [
                $emailReal,
                $nombreReal,
                $emailReal,
                $nombreReal,
                false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | LOCAL / TESTING / STAGING
        |--------------------------------------------------------------------------
        |
        | Nunca hacemos fallback al correo real.
        |
        */

        $emailPrueba = config(
            'consejo-interno.mail.test_recipient'
        );

        if (blank($emailPrueba)) {
            throw new RuntimeException(
                'CI_MAIL_TEST_RECIPIENT debe estar configurado fuera de production.'
            );
        }

        return [
            $emailPrueba,
            config('consejo-interno.mail.test_name'),
            $emailReal,
            $nombreReal,
            true,
        ];
    }

    private function asunto(CiPunto $punto): string
    {
        return 'Resolución de Consejo Interno para solicitud '
            . ($punto->solicitud?->folio ?? '#' . $punto->solicitud_id);
    }

    private function crearPayload(CiPunto $punto): array
    {
        $solicitud = $punto->solicitud;

        return [
            'folio' => $solicitud?->folio,

            'solicitante' => $solicitud?->owner?->fullname(),

            'tipo_solicitud' => $solicitud
                ?->tipoSolicitud
                ?->nombre,

            'resolucion' => $punto->resolucion,

            'estatus' => $solicitud
                ?->estatus
                ?->nombre
                ?? $solicitud?->estatusClave(),

            'nombre_evento' => $solicitud?->nombre_evento,

            'institucion' => $solicitud?->institucion,

            'requiere_recursos' => (bool) $solicitud?->requiere_recursos,

            'observaciones_sacad' => $solicitud
                ?->observaciones_sacad,

            'observaciones_administracion' => $solicitud
                ?->observaciones_administracion,

            'reunion' => $punto->reunion?->titulo,

            'fecha_reunion' => $punto->reunion?->fecha?->format(
                'd/m/Y'
            ),
        ];
    }

    private function crearAdjuntos(CiPunto $punto): array
    {
        if (! config(
            'consejo-interno.mail.attach_reunion_documents'
        )) {
            return [];
        }

        return $punto->reunion?->documentos
            ?->map(fn ($documento) => [
                'disk' => config('filesystems.default'),
                'path' => $documento->path,
                'name' => $documento->original_name,
                'mime' => $documento->mime_type,
            ])
            ->values()
            ->all() ?? [];
    }
}
