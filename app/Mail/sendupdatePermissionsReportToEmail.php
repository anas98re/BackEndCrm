<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendupdatePermissionsReportToEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $updatePermissionsReport;
    public function __construct($updatePermissionsReport)
    {
        $this->updatePermissionsReport = $updatePermissionsReport;
    }

    public function build()
    {
        return $this->view('updatePermissionsReport')
                    ->subject('update Permissions Report');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permissions update report',
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
