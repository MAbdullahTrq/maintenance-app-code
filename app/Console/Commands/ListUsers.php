<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users and their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with('role')->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return;
        }

        $this->info('All Users:');
        $this->info('');

        $headers = ['ID', 'Name', 'Email', 'Role', 'Active', 'Created'];
        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role ? $user->role->name : 'No role',
                $user->is_active ? '✅ Yes' : '❌ No',
                $user->created_at->format('Y-m-d H:i')
            ];
        }

        $this->table($headers, $rows);
    }
} 