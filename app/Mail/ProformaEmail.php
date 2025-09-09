<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProformaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $proforma;
    public $cliente;
    public $vendedor;
    public $pdfPath;

    public function __construct($proforma, $cliente, $vendedor, $pdfPath)
    {
        $this->proforma = $proforma;
        $this->cliente = $cliente;
        $this->vendedor = $vendedor;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Proforma Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.proforma',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('proforma.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
