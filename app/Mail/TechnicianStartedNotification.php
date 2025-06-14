<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;

class TechnicianStartedNotification extends MaintenanceRequestNotification
{
    public function __construct(MaintenanceRequest $maintenance_request)
    {
        parent::__construct(
            maintenance_request: $maintenance_request,
            subject_line: "Maintenance Work Started: {$maintenance_request->title}",
            template: 'emails.maintenance.technician-started'
        );
    }
} 