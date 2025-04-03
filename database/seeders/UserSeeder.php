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
        // Get the admin role
        $adminRole = Role::where('slug', 'admin')->first();
        
        if (!$adminRole) {
            throw new \Exception('Admin role not found. Please run the RoleSeeder first.');
        }

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Get the property manager role
        $propertyManagerRole = Role::where('slug', 'property_manager')->first();
        
        if (!$propertyManagerRole) {
            throw new \Exception('Property Manager role not found. Please run the RoleSeeder first.');
        }

        // Create property manager
        $propertyManager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Property Manager',
                'password' => Hash::make('password'),
                'role_id' => $propertyManagerRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Get the technician role
        $technicianRole = Role::where('slug', 'technician')->first();
        
        if (!$technicianRole) {
            throw new \Exception('Technician role not found. Please run the RoleSeeder first.');
        }

        // Create technician
        $technician = User::updateOrCreate(
            ['email' => 'technician@example.com'],
            [
                'name' => 'Technician',
                'password' => Hash::make('password'),
                'role_id' => $technicianRole->id,
                'email_verified_at' => now(),
                'invited_by' => $propertyManager->id,
            ]
        );
    }
} 