<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    private string $email;
    private string $password;
    private string $comp_id;


    /**
     * Create a new message instance.
     */
    public function __construct(string $email, string $password, string $comp_id)
    {
        $this->email = $email;
        $this->password = $password;
        $this->comp_id = $comp_id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Competition Account Has Been Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        $text = "Your competition account has been created. You can now log in to the competition with the following details:\n\n"
            . "Email: {$this->email}\n"
            . "Password: {$this->password}\n\n"
            . "You can access the competition at: "
            . route('comps.view', ['comp' => $this->comp_id])
            . "\n\n"
            . "Please change your password after logging in for the first time.";

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
