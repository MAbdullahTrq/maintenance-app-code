<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTrialReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trials:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users with expired trials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending trial reminder emails...');

        // Get users who need reminder emails
        $users = User::where('is_active', false)
            ->whereNotNull('account_locked_at')
            ->where('reminder_emails_sent', '<', 3)
            ->get();

        $sentCount = 0;
        foreach ($users as $user) {
            if ($user->canSendReminderEmail()) {
                $this->sendReminderEmail($user);
                $user->incrementReminderEmails();
                $sentCount++;
                $this->line("Sent reminder email to: {$user->email}");
            }
        }

        $this->info("Completed! Sent {$sentCount} reminder emails.");
    }

    /**
     * Send reminder email to user.
     */
    private function sendReminderEmail(User $user)
    {
        $reminderNumber = $user->reminder_emails_sent + 1;
        
        switch ($reminderNumber) {
            case 1:
                $subject = "Still need maintenance support? Your data is safe (for now)";
                $template = 'emails.trial.reminder-1';
                break;
            case 2:
                $subject = "We're holding your account — ready when you are";
                $template = 'emails.trial.reminder-2';
                break;
            case 3:
                $subject = "Last chance to save your account before it's deleted";
                $template = 'emails.trial.reminder-3';
                break;
            default:
                return;
        }

        // TODO: Create email templates and send emails
        // Mail::to($user->email)->send(new TrialReminderMail($user, $reminderNumber));
        
        \Log::info('Trial reminder email would be sent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'reminder_number' => $reminderNumber,
            'subject' => $subject
        ]);
    }
}
