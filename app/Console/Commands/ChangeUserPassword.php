<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangeUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-password {email} {--password= : The new password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a user\'s password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->option('password');

        // Find the user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        // Get password if not provided
        if (!$password) {
            $password = $this->secret('Enter new password');
            if (!$password) {
                $this->error('Password cannot be empty.');
                return;
            }

            $confirmPassword = $this->secret('Confirm new password');
            if ($password !== $confirmPassword) {
                $this->error('Passwords do not match.');
                return;
            }
        }

        // Validate password length
        if (strlen($password) < 6) {
            $this->error('Password must be at least 6 characters long.');
            return;
        }

        // Update password
        $user->password = Hash::make($password);
        $user->save();

        $this->info("✅ Successfully changed password for {$user->name} ({$user->email})");
    }
} 