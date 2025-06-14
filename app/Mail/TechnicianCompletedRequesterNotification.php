<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;

class TechnicianCompletedRequesterNotification extends MaintenanceRequestNotification
{
    public function __construct(MaintenanceRequest $maintenance_request)
    {
        parent::__construct(
            maintenance_request: $maintenance_request,
            subject_line: "Maintenance Work Completed: {$maintenance_request->title}",
            template: 'emails.maintenance.technician-completed-requester'
        );
    }
} 