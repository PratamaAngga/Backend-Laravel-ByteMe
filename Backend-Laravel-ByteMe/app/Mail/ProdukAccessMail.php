<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProdukAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $username,
        public string $namaProduk,
        public string $linkAkses,
        public string $pesananId,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Access The Product - ' . $this->namaProduk,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.produk-access',
        );
    }
}
