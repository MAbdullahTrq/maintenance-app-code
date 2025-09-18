<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TechnicianWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $technician;
    public $manager;
    public $verification_token;

    /**
     * Create a new message instance.
     */
    public function __construct(User $technician, User $manager, string $verification_token)
    {
        $this->technician = $technician;
        $this->manager = $manager;
        $this->verification_token = $verification_token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to MaintainXtra - Verify Your Account',
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address', 'noreply@maintainxtra.com'),
                config('mail.from.name', 'MaintainXtra Support')
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.technician.welcome',
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