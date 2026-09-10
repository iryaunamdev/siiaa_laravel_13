<?php

namespace App\Mail\ConsejoInterno;

use App\Models\ConsejoInterno\CiNotificacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResolucionSolicitudMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public CiNotificacion $notificacion
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notificacion->asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.consejo-interno.resolucion-solicitud',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return collect($this->notificacion->adjuntos ?? [])
            ->map(function (array $adjunto) {
                $attachment = Attachment::fromStorageDisk(
                    $adjunto['disk'],
                    $adjunto['path']
                )->as($adjunto['name']);

                if (filled($adjunto['mime'] ?? null)) {
                    $attachment->withMime($adjunto['mime']);
                }

                return $attachment;
            })
            ->all();
    }
}
