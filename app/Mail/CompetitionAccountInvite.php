<?php

namespace App\Mail;

use App\Models\Competition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionAccountInvite extends Mailable
{
    use Queueable, SerializesModels;

    private Competition $competition;
    private array $access;


    /**
     * Create a new message instance.
     */
    public function __construct(Competition $competition, array $access)
    {
        $this->competition = $competition;
        $this->access = $access;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to a competition',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        $text = "You have been invited to join the competition \"{$this->competition->name}\".\n\n";
        $text .= "You can access the competition using the following link: ";
        $text .= route('comps.view', ['comp' => $this->competition->id]) . "\n\n";
        $text .= "You have been granted the following access:\n";

        // Access list here will be the display values instead of keys
        foreach ($this->access as $access) {
            $text .= "- " . $access . "\n";
        }

        $text .= "\nIf you have any questions, please contact the competition organizer.";

        return new Content(
            view: 'mail.base',
            with: [
                'text' => $text,
            ],
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
}
