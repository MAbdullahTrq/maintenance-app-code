<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;
use App\Models\RequestComment;

class TechnicianCommentNotification extends MaintenanceRequestNotification
{
    public function __construct(
        MaintenanceRequest $maintenance_request,
        public RequestComment $comment
    ) {
        parent::__construct(
            maintenance_request: $maintenance_request,
            subject_line: "New Comment on Maintenance Request: {$maintenance_request->title}",
            template: 'emails.maintenance.technician-comment'
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