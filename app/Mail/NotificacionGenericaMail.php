<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionGenericaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $asuntoMensaje,
        public string $cuerpo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asuntoMensaje,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion-generica',
            with: [
                'asuntoMensaje' => $this->asuntoMensaje,
                'cuerpo' => $this->cuerpo,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
