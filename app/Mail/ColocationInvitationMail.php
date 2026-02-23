<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ColocationInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invitation $invitation
    ) {
        $this->invitation->loadMissing('colocation');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Colocation Invitation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.colocation-invitation',
        );
    }
}
