<?php

namespace App\Traits;

use Illuminate\Mail\Message;
use Symfony\Component\Mime\Email;

trait HasSmtp2goTracking
{
    /**
     * Add SMTP2GO tracking headers to the email.
     */
    protected function addSmtp2goTracking($message): void
    {
        $config = config('smtp2go.tracking', [
            'opens' => false,
            'clicks' => false,
            'unsubscribes' => false,
            'bounces' => false,
        ]);

        // Force sender name through SMTP2GO headers
        $this->addHeader($message, 'X-SMTP2GO-FROM-NAME', config('mail.from.name', 'MaintainXtra Support'));

        if ($config['opens']) {
            $this->addHeader($message, 'X-SMTP2GO-TRACK-OPENS', '1');
        }

        if ($config['clicks']) {
            $this->addHeader($message, 'X-SMTP2GO-TRACK-CLICKS', '1');
        }

        if ($config['unsubscribes']) {
            $this->addHeader($message, 'X-SMTP2GO-TRACK-UNSUBSCRIBES', '1');
        }

        if ($config['bounces']) {
            $this->addHeader($message, 'X-SMTP2GO-TRACK-BOUNCES', '1');
        }
    }

    /**
     * Add custom SMTP2GO headers to the email.
     */
    protected function addSmtp2goHeaders($message, array $headers = []): void
    {
        foreach ($headers as $key => $value) {
            $this->addHeader($message, "X-SMTP2GO-{$key}", $value);
        }
    }

    /**
     * Add header to message, handling both Illuminate\Mail\Message and Symfony\Component\Mime\Email
     */
    private function addHeader($message, string $name, string $value): void
    {
        if ($message instanceof Message) {
            // Laravel's Message wrapper
            $message->getHeaders()->add($name, $value);
        } elseif ($message instanceof Email) {
            // Symfony's Email object
            $message->getHeaders()->addTextHeader($name, $value);
        }
    }
} 