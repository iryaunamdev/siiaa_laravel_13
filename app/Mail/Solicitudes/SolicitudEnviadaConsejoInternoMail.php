<?php

namespace App\Mail\Solicitudes;

use App\Models\Solicitudes\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudEnviadaConsejoInternoMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Solicitud $solicitud,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud recibida para revisión',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitudes.enviada-consejo-interno',
        );
    }
}