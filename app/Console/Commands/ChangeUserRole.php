<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class ChangeUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a user\'s role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleSlug = $this->argument('role');

        // Find the user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        // Find the role
        $role = Role::where('slug', $roleSlug)->first();
        if (!$role) {
            $this->error("Role '{$roleSlug}' not found.");
            $this->info('Available roles:');
            Role::all()->each(function($role) {
                $this->info("- {$role->slug} ({$role->name})");
            });
            return;
        }

        // Change the role
        $oldRole = $user->role ? $user->role->name : 'No role';
        $user->role_id = $role->id;
        $user->save();

        $this->info("✅ Successfully changed {$user->name}'s role from '{$oldRole}' to '{$role->name}'");
    }
} 