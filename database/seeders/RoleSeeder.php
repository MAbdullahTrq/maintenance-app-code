<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with full access to all features',
            ],
            [
                'name' => 'Property Manager',
                'slug' => 'property_manager',
                'description' => 'Handles work orders, approves requests, and assigns tasks.',
            ],
            [
                'name' => 'Technician',
                'slug' => 'technician',
                'description' => 'Completes maintenance tasks assigned by the Property Manager.',
            ],
            [
                'name' => 'Team Member',
                'slug' => 'team_member',
                'description' => 'Basic team member with limited access to view and manage properties.',
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Can only view properties and requests, no editing permissions.',
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can view and edit properties and requests, but cannot manage team members.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
} 