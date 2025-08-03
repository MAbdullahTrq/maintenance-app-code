<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TechnicianAssignmentSmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The maintenance request instance.
     */
    protected MaintenanceRequest $maintenance_request;

    /**
     * Create a new notification instance.
     */
    public function __construct(MaintenanceRequest $maintenance_request)
    {
        $this->maintenance_request = $maintenance_request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'maintenance_request_id' => $this->maintenance_request->id,
            'title' => $this->maintenance_request->title,
            'property_name' => $this->maintenance_request->property->name ?? 'Unknown Property',
            'priority' => $this->maintenance_request->priority,
            'due_date' => $this->maintenance_request->due_date?->toDateString(),
            'type' => 'technician_assignment_sms',
        ];
    }

    /**
     * Send the SMS notification.
     *
     * @param object $notifiable
     * @return void
     */
    public function sendSmsNotification(object $notifiable): void
    {
        $sms_service = app(SmsService::class);
        $sms_service->sendTechnicianAssignmentNotification($this->maintenance_request, $notifiable);
    }
} 