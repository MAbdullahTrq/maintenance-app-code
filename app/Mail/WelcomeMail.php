<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(
            config('mail.from.address', 'noreply@maintainxtra.com'),
            config('mail.from.name', 'MaintainXtra Support')
        )->subject('Welcome to MaintainXtra!')
        ->markdown('emails.auth.welcome', [
            'user' => $this->user,
        ]);
    }
} 