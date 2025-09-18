<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationToken;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationToken)
    {
        $this->user = $user;
        $this->verificationToken = $verificationToken;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email - MaintainXtra',
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address', 'noreply@maintainxtra.com'),
                config('mail.from.name', 'MaintainXtra Support')
            ),
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(
            config('mail.from.address', 'noreply@maintainxtra.com'),
            config('mail.from.name', 'MaintainXtra Support')
        )->markdown('emails.auth.verify-email', [
            'user' => $this->user,
            'verificationToken' => $this->verificationToken,
        ]);
    }
} 