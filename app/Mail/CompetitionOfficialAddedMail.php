<?php

namespace App\Mail;

use App\Models\Competition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionOfficialAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  bool  $newAccount  the account was just made for them, so they've never signed in
     */
    public function __construct(
        public Competition $competition,
        public string $role,
        public bool $newAccount = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been added to {$this->competition->name} on Digital Judge",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.official-added',
            with: [
                'competition' => $this->competition,
                'roleName' => strtolower(Competition::$officialRoles[$this->role] ?? 'official'),
                'newAccount' => $this->newAccount,
                'url' => route('judge.login'),
            ]
        );
    }
}
