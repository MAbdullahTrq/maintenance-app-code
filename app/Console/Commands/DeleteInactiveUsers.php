<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-inactive {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all inactive users from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get inactive users
        $inactiveUsers = User::where('is_active', false)->get();
        $count = $inactiveUsers->count();

        if ($count === 0) {
            $this->info('No inactive users found.');
            return 0;
        }

        $this->info("Found {$count} inactive users:");
        
        // Display users that will be deleted
        $this->table(
            ['ID', 'Name', 'Email', 'Role'],
            $inactiveUsers->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role?->name ?? 'No Role'
                ];
            })->toArray()
        );

        // Confirmation (unless --force is used)
        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to delete these {$count} inactive users? This action cannot be undone.")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Delete users
        try {
            $deletedCount = User::where('is_active', false)->delete();
            
            $this->info("Successfully deleted {$deletedCount} inactive users.");
            
            // Log the action
            \Log::info("Deleted {$deletedCount} inactive users", [
                'command' => 'users:delete-inactive',
                'deleted_users' => $inactiveUsers->pluck('email')->toArray()
            ]);
            
        } catch (\Exception $e) {
            $this->error("Error deleting users: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 