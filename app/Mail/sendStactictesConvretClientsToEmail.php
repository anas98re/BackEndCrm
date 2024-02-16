<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendStactictesConvretClientsToEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $ClintsStaticts;
    public function __construct($ClintsStaticts)
    {
        $this->ClintsStaticts = $ClintsStaticts;
    }

    public function build()
    {
        return $this->from('your-email@example.com', 'Smart CRM')
                    ->view('StactictesConvretClientsTothabetEmail')
                    ->subject('Stactictes of Convret');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Stactictes of Convret',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'emailsVerification_code',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
