<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Console\Command;

class AddSubscriptionForManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-subscription-for-manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an active subscription for the manager@example.com user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', 'manager@example.com')->first();

        if (!$user) {
            $this->error('User with email manager@example.com not found!');
            return 1;
        }

        // Get the highest tier subscription plan
        $plan = SubscriptionPlan::orderBy('price', 'desc')->first();

        if (!$plan) {
            $this->error('No subscription plans found!');
            return 1;
        }

        // Check if user already has an active subscription
        $existingSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if ($existingSubscription) {
            $this->info('User already has an active subscription until ' . $existingSubscription->ends_at->format('Y-m-d'));
            return 0;
        }

        // Create a new subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'payment_method' => 'manual',
            'status' => 'active',
        ]);

        $this->info('Subscription added successfully!');
        $this->info('Plan: ' . $plan->name);
        $this->info('Valid until: ' . $subscription->ends_at->format('Y-m-d'));

        return 0;
    }
}
