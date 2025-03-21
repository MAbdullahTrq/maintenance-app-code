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
                'name' => 'Super Property Manager',
                'slug' => 'super_manager',
                'description' => 'Oversees the entire platform and manages client accounts.',
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
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
} 