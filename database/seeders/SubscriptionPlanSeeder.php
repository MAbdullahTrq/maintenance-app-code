<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for small property managers with limited properties.',
                'price' => 29.99,
                'duration_in_days' => 30,
                'property_limit' => 3,
                'technician_limit' => 5,
            ],
            [
                'name' => 'Standard',
                'description' => 'Ideal for medium-sized property management companies.',
                'price' => 59.99,
                'duration_in_days' => 30,
                'property_limit' => 10,
                'technician_limit' => 15,
            ],
            [
                'name' => 'Premium',
                'description' => 'Best for large property management companies with multiple properties.',
                'price' => 99.99,
                'duration_in_days' => 30,
                'property_limit' => 25,
                'technician_limit' => 30,
            ],
            [
                'name' => 'Annual Basic',
                'description' => 'Annual subscription for small property managers with limited properties.',
                'price' => 299.99,
                'duration_in_days' => 365,
                'property_limit' => 3,
                'technician_limit' => 5,
            ],
            [
                'name' => 'Annual Standard',
                'description' => 'Annual subscription for medium-sized property management companies.',
                'price' => 599.99,
                'duration_in_days' => 365,
                'property_limit' => 10,
                'technician_limit' => 15,
            ],
            [
                'name' => 'Annual Premium',
                'description' => 'Annual subscription for large property management companies with multiple properties.',
                'price' => 999.99,
                'duration_in_days' => 365,
                'property_limit' => 25,
                'technician_limit' => 30,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
} 