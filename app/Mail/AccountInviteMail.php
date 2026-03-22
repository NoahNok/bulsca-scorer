<?php

namespace App\Mail;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use BaconQrCode\Common\Mode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    private Model $too;
    private string $url;

    /**
     * Create a new message instance.
     */
    public function __construct(Model $too, string $url)
    {
        $this->too = $too;
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Invite Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invite',
            with: ['to' => $this->getToString(), 'url' => $this->url]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    private function getToString()
    {
        if ($this->too instanceof Organisation) {
            return "join {$this->too->name}";
        }

        if ($this->too instanceof Competition) {
            return $this->too->name ?? 'Unknown';
        }

        return 'Scoring.Events';
    }
}
