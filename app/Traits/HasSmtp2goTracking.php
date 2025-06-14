<?php

namespace App\Traits;

use Illuminate\Mail\Message;

trait HasSmtp2goTracking
{
    /**
     * Add SMTP2GO tracking headers to the email.
     */
    protected function addSmtp2goTracking(Message $message): void
    {
        $config = config('smtp2go.tracking');

        if ($config['opens']) {
            $message->getHeaders()->add('X-SMTP2GO-TRACK-OPENS', '1');
        }

        if ($config['clicks']) {
            $message->getHeaders()->add('X-SMTP2GO-TRACK-CLICKS', '1');
        }

        if ($config['unsubscribes']) {
            $message->getHeaders()->add('X-SMTP2GO-TRACK-UNSUBSCRIBES', '1');
        }

        if ($config['bounces']) {
            $message->getHeaders()->add('X-SMTP2GO-TRACK-BOUNCES', '1');
        }
    }

    /**
     * Add custom SMTP2GO headers to the email.
     */
    protected function addSmtp2goHeaders(Message $message, array $headers = []): void
    {
        foreach ($headers as $key => $value) {
            $message->getHeaders()->add("X-SMTP2GO-{$key}", $value);
        }
    }
} 