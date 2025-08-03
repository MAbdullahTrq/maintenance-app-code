<?php

namespace App\Services;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Exceptions\TwilioException;

class SmsService
{
    /**
     * The Twilio client instance.
     */
    protected TwilioClient $twilio_client;

    /**
     * Create a new SMS service instance.
     */
    public function __construct(TwilioClient $twilio_client)
    {
        $this->twilio_client = $twilio_client;
    }

    /**
     * Send an SMS message.
     *
     * @param string $to_phone_number
     * @param string $message
     * @return bool
     */
    public function sendSms(string $to_phone_number, string $message): bool
    {
        if (!config('twilio.sms.enabled')) {
            Log::info('SMS sending is disabled. Message would have been sent to: ' . $to_phone_number);
            return true;
        }

        try {
            $this->twilio_client->messages->create(
                $to_phone_number,
                [
                    'from' => config('twilio.sms.from_number'),
                    'body' => $message,
                ]
            );

            Log::info('SMS sent successfully to: ' . $to_phone_number);
            return true;
        } catch (TwilioException $e) {
            Log::error('Failed to send SMS to ' . $to_phone_number . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send technician assignment notification via SMS.
     *
     * @param MaintenanceRequest $maintenance_request
     * @param User $technician
     * @return bool
     */
    public function sendTechnicianAssignmentNotification(MaintenanceRequest $maintenance_request, User $technician): bool
    {
        if (!$technician->phone) {
            Log::warning('Cannot send SMS notification: Technician has no phone number', [
                'technician_id' => $technician->id,
                'request_id' => $maintenance_request->id,
            ]);
            return false;
        }

        $message = $this->formatTechnicianAssignmentMessage($maintenance_request);
        
        return $this->sendSms($technician->phone, $message);
    }

    /**
     * Format the technician assignment SMS message.
     *
     * @param MaintenanceRequest $maintenance_request
     * @return string
     */
    protected function formatTechnicianAssignmentMessage(MaintenanceRequest $maintenance_request): string
    {
        $property_name = $maintenance_request->property->name ?? 'Unknown Property';
        $priority = ucfirst($maintenance_request->priority);
        $due_date = $maintenance_request->due_date 
            ? $maintenance_request->due_date->format('M d, Y') 
            : 'Not specified';

        return "🔧 New Maintenance Request Assigned\n\n" .
               "Property: {$property_name}\n" .
               "Title: {$maintenance_request->title}\n" .
               "Priority: {$priority}\n" .
               "Due Date: {$due_date}\n\n" .
               "Please review and take appropriate action.\n\n" .
               "MaintainXtra";
    }

    /**
     * Validate phone number format.
     *
     * @param string $phone_number
     * @return bool
     */
    public function isValidPhoneNumber(string $phone_number): bool
    {
        // Basic validation - can be enhanced with more sophisticated validation
        $phone_number = preg_replace('/[^0-9+]/', '', $phone_number);
        
        // Check if it starts with + and has at least 10 digits
        return preg_match('/^\+[1-9]\d{1,14}$/', $phone_number);
    }

    /**
     * Format phone number for Twilio.
     *
     * @param string $phone_number
     * @return string
     */
    public function formatPhoneNumber(string $phone_number): string
    {
        // Remove all non-digit characters except +
        $phone_number = preg_replace('/[^0-9+]/', '', $phone_number);
        
        // If it doesn't start with +, assume it's a US number
        if (!str_starts_with($phone_number, '+')) {
            $phone_number = '+1' . $phone_number;
        }
        
        return $phone_number;
    }
} 