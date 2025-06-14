<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;
use App\Traits\HasSmtp2goTracking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceRequestNotification extends Mailable
{
    use Queueable, SerializesModels, HasSmtp2goTracking;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public MaintenanceRequest $maintenance_request,
        public string $subject_line,
        public string $template
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject_line,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: $this->template,
            with: [
                'maintenance_request' => $this->maintenance_request,
            ],
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->withSymfonyMessage(function ($message) {
            $this->addSmtp2goTracking($message);
            
            // Add custom headers for maintenance request
            $this->addSmtp2goHeaders($message, [
                'REQUEST-ID' => $this->maintenance_request->id,
                'REQUEST-TYPE' => class_basename($this),
                'PRIORITY' => $this->maintenance_request->priority,
            ]);
        });
    }
} 