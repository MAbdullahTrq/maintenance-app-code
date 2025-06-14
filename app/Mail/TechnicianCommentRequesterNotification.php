<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;
use App\Models\RequestComment;
use Illuminate\Mail\Mailables\Content;

class TechnicianCommentRequesterNotification extends MaintenanceRequestNotification
{
    public function __construct(
        MaintenanceRequest $maintenance_request,
        public RequestComment $comment
    ) {
        parent::__construct(
            maintenance_request: $maintenance_request,
            subject_line: "Update on Maintenance Request: {$maintenance_request->title}",
            template: 'emails.maintenance.technician-comment-requester'
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
                'comment' => $this->comment,
            ],
        );
    }
} 