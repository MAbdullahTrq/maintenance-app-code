<?php

if (!function_exists('send_sms_notification')) {
    /**
     * Send an SMS notification to a user.
     *
     * @param \App\Models\User $user
     * @param string $message
     * @return bool
     */
    function send_sms_notification($user, string $message): bool
    {
        if (!$user->phone) {
            return false;
        }

        $sms_service = app(\App\Services\SmsService::class);
        return $sms_service->sendSms($user->phone, $message);
    }
}

if (!function_exists('format_phone_number')) {
    /**
     * Format a phone number for SMS sending.
     *
     * @param string $phone_number
     * @return string
     */
    function format_phone_number(string $phone_number): string
    {
        $sms_service = app(\App\Services\SmsService::class);
        return $sms_service->formatPhoneNumber($phone_number);
    }
}

if (!function_exists('is_valid_phone_number')) {
    /**
     * Check if a phone number is valid for SMS sending.
     *
     * @param string $phone_number
     * @return bool
     */
    function is_valid_phone_number(string $phone_number): bool
    {
        $sms_service = app(\App\Services\SmsService::class);
        return $sms_service->isValidPhoneNumber($phone_number);
    }
}

if (!function_exists('can_delete_owner')) {
    /**
     * Check if an owner can be deleted (has no properties).
     *
     * @param \App\Models\Owner $owner
     * @return bool
     */
    function can_delete_owner($owner): bool
    {
        return $owner->properties()->count() === 0;
    }
}

if (!function_exists('get_owner_properties_list')) {
    /**
     * Get a formatted list of properties owned by an owner.
     *
     * @param \App\Models\Owner $owner
     * @return string
     */
    function get_owner_properties_list($owner): string
    {
        return $owner->properties->pluck('name')->implode(', ');
    }
} 