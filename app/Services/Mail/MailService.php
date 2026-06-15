<?php

namespace App\Services\Mail;

use App\Mail\Solicitudes\SolicitudAprobadaMail;
use App\Mail\Solicitudes\SolicitudCerradaMail;
use App\Mail\Solicitudes\SolicitudEnviadaConsejoInternoMail;
use App\Mail\Solicitudes\SolicitudEnviadaSolicitanteMail;
use App\Mail\Solicitudes\SolicitudRechazadaMail;
use App\Models\Solicitudes\Solicitud;
use Illuminate\Support\Facades\Mail;

class MailService implements MailServiceInterface
{
    public function enviarSolicitudEnviadaSolicitante(Solicitud $solicitud): void
    {
        $destinatarios = $this->destinatariosSolicitante($solicitud);

        if (empty($destinatarios)) {
            return;
        }

        Mail::to($destinatarios)
            ->send(new SolicitudEnviadaSolicitanteMail($solicitud));
    }

    public function enviarSolicitudEnviadaConsejoInterno(Solicitud $solicitud): void
    {
        $destinatarios = $this->destinatariosConsejoInterno();

        if (empty($destinatarios)) {
            return;
        }

        Mail::to($destinatarios)
            ->send(new SolicitudEnviadaConsejoInternoMail($solicitud));
    }

    public function enviarSolicitudAprobadaSolicitante(Solicitud $solicitud): void
    {
        $destinatarios = $this->destinatariosSolicitante($solicitud);

        if (empty($destinatarios)) {
            return;
        }

        Mail::to($destinatarios)
            ->send(new SolicitudAprobadaMail($solicitud));
    }

    public function enviarSolicitudRechazadaSolicitante(Solicitud $solicitud): void
    {
        $destinatarios = $this->destinatariosSolicitante($solicitud);

        if (empty($destinatarios)) {
            return;
        }

        Mail::to($destinatarios)
            ->send(new SolicitudRechazadaMail($solicitud));
    }

    public function enviarSolicitudCerradaSolicitante(Solicitud $solicitud): void
    {
        $destinatarios = $this->destinatariosSolicitante($solicitud);

        if (empty($destinatarios)) {
            return;
        }

        Mail::to($destinatarios)
            ->send(new SolicitudCerradaMail($solicitud));
    }

    /**
     * Resuelve destinatarios del solicitante.
     *
     * En local/testing se redirige a correos de prueba.
     * En producción se usa el correo real del propietario de la solicitud.
     */
    protected function destinatariosSolicitante(Solicitud $solicitud): array
    {
        if (! $this->usarDestinatariosReales()) {
            return $this->destinatariosPrueba();
        }

        $correo = $solicitud->owner?->email
            ?? $solicitud->owner?->correo
            ?? $solicitud->owner?->user?->email
            ?? null;

        return $correo ? [$correo] : [];
    }

    /**
     * Resuelve destinatarios del Consejo Interno.
     *
     * En local/testing se redirige a correos de prueba.
     * En producción se usan los correos reales configurados.
     */
    protected function destinatariosConsejoInterno(): array
    {
        if (! $this->usarDestinatariosReales()) {
            return $this->destinatariosPrueba();
        }

        return $this->normalizarListaCorreos(
            config('siiaa.mail.consejo_interno', [])
        );
    }

    /**
     * Determina si se deben usar destinatarios reales.
     *
     * Regla aprobada:
     * - APP_ENV=production: destinatarios reales.
     * - Cualquier otro entorno: destinatarios de prueba.
     */
    protected function usarDestinatariosReales(): bool
    {
        return (bool) config('siiaa.mail.use_real_recipients');
    }

    /**
     * Correos de prueba para entorno local/desarrollo.
     */
    protected function destinatariosPrueba(): array
    {
        return $this->normalizarListaCorreos(
            config('siiaa.mail.test_recipients', [])
        );
    }

    /**
     * Normaliza correos desde array o string separado por comas.
     */
    protected function normalizarListaCorreos(array|string|null $correos): array
    {
        if (is_string($correos)) {
            $correos = explode(',', $correos);
        }

        return collect($correos ?? [])
            ->map(fn($correo) => trim((string) $correo))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}