<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CotizacionEnviada extends Mailable
{
    use Queueable, SerializesModels;
    public $cotizacion;
    public $cliente;
    public $pdfPath;

    public function __construct($cotizacion, $cliente, $pdfPath)
    {
        $this->cotizacion = $cotizacion;
        $this->cliente = $cliente;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotización enviada',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cotizacion',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('cotizacion.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
