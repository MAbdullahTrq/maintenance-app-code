<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Http;

class Smtp2goServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/smtp2go.php', 'smtp2go'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/smtp2go.php' => config_path('smtp2go.php'),
        ], 'smtp2go-config');

        // Extend the mail manager to use SMTP2GO API
        $this->app->make(MailManager::class)->extend('smtp2go', function ($app) {
            $config = $app['config']->get('smtp2go');
            
            return new class($config) extends \Illuminate\Mail\Transport\Transport
            {
                protected $config;
                protected $apiEndpoint;

                public function __construct($config)
                {
                    $this->config = $config;
                    $this->apiEndpoint = $config['api']['endpoint'] . '/email/send';
                }

                public function send(\Symfony\Component\Mailer\SentMessage $message, \Symfony\Component\Mime\Email $email): void
                {
                    $payload = [
                        'api_key' => $this->config['api']['key'],
                        'to' => $this->getRecipients($email),
                        'sender' => $this->getSender($email),
                        'subject' => $email->getSubject(),
                        'html_body' => $email->getHtmlBody(),
                        'text_body' => $email->getTextBody(),
                    ];

                    // Add custom headers
                    foreach ($email->getHeaders()->all() as $header) {
                        if (str_starts_with($header->getName(), 'X-SMTP2GO-')) {
                            $payload[strtolower(str_replace('X-SMTP2GO-', '', $header->getName()))] = $header->getBodyAsString();
                        }
                    }

                    // Add attachments if any
                    if ($attachments = $email->getAttachments()) {
                        $payload['attachments'] = $this->formatAttachments($attachments);
                    }

                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($this->apiEndpoint, $payload);

                    if (!$response->successful()) {
                        throw new \Exception('SMTP2GO API Error: ' . $response->body());
                    }
                }

                protected function getRecipients($email): array
                {
                    $recipients = [];
                    foreach ($email->getTo() as $address) {
                        $recipients[] = $address->getAddress();
                    }
                    return $recipients;
                }

                protected function getSender($email): string
                {
                    return $email->getFrom()[0]->getAddress();
                }

                protected function formatAttachments($attachments): array
                {
                    $formatted = [];
                    foreach ($attachments as $attachment) {
                        $formatted[] = [
                            'filename' => $attachment->getFilename(),
                            'content' => base64_encode($attachment->getBody()),
                            'type' => $attachment->getContentType(),
                        ];
                    }
                    return $formatted;
                }
            };
        });
    }
} 