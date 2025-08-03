<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

class SendTestSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone : The phone number to send test SMS to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test SMS message using Twilio';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $sms_service): int
    {
        $phone_number = $this->argument('phone');
        
        if (!$sms_service->isValidPhoneNumber($phone_number)) {
            $this->error('Invalid phone number format. Please use international format (e.g., +1234567890)');
            return 1;
        }

        $formatted_phone = $sms_service->formatPhoneNumber($phone_number);
        $message = "🧪 Test SMS from MaintainXtra\n\nThis is a test message to verify SMS functionality.\n\nSent at: " . now()->format('Y-m-d H:i:s');

        $this->info("Sending test SMS to: {$formatted_phone}");
        
        if ($sms_service->sendSms($formatted_phone, $message)) {
            $this->info('✅ Test SMS sent successfully!');
            return 0;
        } else {
            $this->error('❌ Failed to send test SMS. Check logs for details.');
            return 1;
        }
    }
} 