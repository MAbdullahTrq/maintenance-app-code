<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ProcessTrialExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trials:process-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process trial expiration and lock accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing trial expiration...');

        // Lock accounts that are past grace period
        $usersToLock = User::where('trial_expires_at', '<=', now()->subDays(7))
            ->whereNull('account_locked_at')
            ->whereDoesntHave('subscriptions', function ($query) {
                $query->where('status', 'active');
            })
            ->where('is_active', true)
            ->get();

        $lockedCount = 0;
        foreach ($usersToLock as $user) {
            $user->lockAccount();
            $lockedCount++;
            $this->line("Locked account for user: {$user->email}");
        }

        // Delete accounts that are past the 90-day data retention period
        $usersToDelete = User::where('data_deletion_at', '<=', now())
            ->where('is_active', false)
            ->get();

        $deletedCount = 0;
        foreach ($usersToDelete as $user) {
            // Store email for GHL pipeline before deletion
            $email = $user->email;
            
            // Delete user and associated data
            $user->delete();
            $deletedCount++;
            
            $this->line("Deleted account for user: {$email}");
            
            // TODO: Add to GHL pipeline for marketing
            // \Log::info('User deleted, add to GHL pipeline', ['email' => $email]);
        }

        $this->info("Completed! Locked {$lockedCount} accounts, deleted {$deletedCount} accounts.");
    }
}
