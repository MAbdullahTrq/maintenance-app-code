<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $superManagerRole = Role::where('slug', 'super_manager')->first();
        $propertyManagerRole = Role::where('slug', 'property_manager')->first();
        $technicianRole = Role::where('slug', 'technician')->first();

        // Create super manager
        $superManager = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superManagerRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create property manager
        $propertyManager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Property Manager',
                'password' => Hash::make('password'),
                'role_id' => $propertyManagerRole->id,
                'invited_by' => $superManager->id,
                'email_verified_at' => now(),
            ]
        );

        // Create technicians
        $technicians = [
            [
                'name' => 'John Technician',
                'email' => 'john@example.com',
            ],
            [
                'name' => 'Jane Technician',
                'email' => 'jane@example.com',
            ],
        ];

        foreach ($technicians as $technicianData) {
            User::updateOrCreate(
                ['email' => $technicianData['email']],
                [
                    'name' => $technicianData['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $technicianRole->id,
                    'invited_by' => $propertyManager->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
} 