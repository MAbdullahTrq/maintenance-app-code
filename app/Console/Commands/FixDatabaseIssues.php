<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixDatabaseIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-issues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix common database issues like duplicate phone numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database fixes...');

        // Fix duplicate phone numbers
        $this->fixDuplicatePhoneNumbers();

        // Check for other potential issues
        $this->checkForOtherIssues();

        $this->info('Database fixes completed!');
    }

    /**
     * Fix duplicate phone numbers in users table
     */
    private function fixDuplicatePhoneNumbers()
    {
        $this->info('Checking for duplicate phone numbers...');

        $duplicates = DB::table('users')
            ->select('phone', DB::raw('COUNT(*) as count'))
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->groupBy('phone')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->count() > 0) {
            $this->warn("Found {$duplicates->count()} duplicate phone numbers. Fixing...");

            foreach ($duplicates as $duplicate) {
                $this->line("Processing duplicate phone: {$duplicate->phone}");

                // Get all users with this phone number
                $users = DB::table('users')
                    ->where('phone', $duplicate->phone)
                    ->orderBy('id')
                    ->get();

                // Keep the first user's phone number, set others to null
                $firstUser = $users->first();
                $otherUsers = $users->skip(1);

                foreach ($otherUsers as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['phone' => null]);
                    
                    $this->line("  - Set phone to null for user ID: {$user->id}");
                }
            }

            $this->info('Duplicate phone numbers fixed!');
        } else {
            $this->info('No duplicate phone numbers found.');
        }
    }

    /**
     * Check for other potential database issues
     */
    private function checkForOtherIssues()
    {
        $this->info('Checking for other potential issues...');

        // Check if team_invitations table exists
        if (!Schema::hasTable('team_invitations')) {
            $this->warn('team_invitations table does not exist. Run migrations first.');
        } else {
            $this->info('team_invitations table exists.');
        }

        // Check if roles table has the new team roles
        $teamRoles = ['team_member', 'viewer', 'editor'];
        foreach ($teamRoles as $role) {
            $exists = DB::table('roles')->where('slug', $role)->exists();
            if (!$exists) {
                $this->warn("Role '{$role}' does not exist. Run RoleSeeder.");
            } else {
                $this->info("Role '{$role}' exists.");
            }
        }

        // Check for users without roles
        $usersWithoutRoles = DB::table('users')
            ->whereNull('role_id')
            ->count();

        if ($usersWithoutRoles > 0) {
            $this->warn("Found {$usersWithoutRoles} users without roles assigned.");
        } else {
            $this->info('All users have roles assigned.');
        }
    }
}
