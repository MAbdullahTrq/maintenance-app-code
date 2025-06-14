<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;

class TechnicianAssignedNotification extends MaintenanceRequestNotification
{
    public function __construct(MaintenanceRequest $maintenance_request)
    {
        parent::__construct(
            maintenance_request: $maintenance_request,
            subject_line: "New Maintenance Request Assigned: {$maintenance_request->title}",
            template: 'emails.maintenance.technician-assigned'
        );
    }
} 