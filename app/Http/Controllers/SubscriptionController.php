<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the subscription plans.
     */
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        $user = Auth::user();
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();
        
        return view('subscriptions.plans', compact('plans', 'activeSubscription'));
    }

    /**
     * Show the checkout page for a subscription plan.
     */
    public function checkout(SubscriptionPlan $plan)
    {
        return view('subscriptions.checkout', compact('plan'));
    }

    /**
     * Create a PayPal order for the subscription.
     */
    public function createPayPalOrder(Request $request, SubscriptionPlan $plan)
    {
        $client = $this->getPayPalClient();

        $orderRequest = new OrdersCreateRequest();
        $orderRequest->prefer('return=representation');
        
        $orderRequest->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'subscription_' . $plan->id,
                    'description' => $plan->name . ' Subscription',
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $plan->price,
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => route('subscription.capture', $plan->id),
                'cancel_url' => route('subscription.plans'),
            ],
        ];

        try {
            $response = $client->execute($orderRequest);
            
            // If this is an AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'id' => $response->result->id,
                    'status' => $response->result->status,
                    'links' => collect($response->result->links)
                        ->where('rel', 'approve')
                        ->first()->href,
                ]);
            }
            
            // For form submissions, redirect to PayPal approval URL
            $approvalUrl = collect($response->result->links)
                ->where('rel', 'approve')
                ->first()->href;
                
            return redirect()->away($approvalUrl);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            
            return redirect()->route('subscription.plans')
                ->with('error', 'Failed to create PayPal order: ' . $e->getMessage());
        }
    }

    /**
     * Capture the PayPal order and create the subscription.
     */
    public function capturePayPalOrder(Request $request, SubscriptionPlan $plan)
    {
        if (!$request->has('token')) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Payment failed or was cancelled.');
        }

        $client = $this->getPayPalClient();
        $ordersCaptureRequest = new OrdersCaptureRequest($request->token);
        $ordersCaptureRequest->prefer('return=representation');

        try {
            $response = $client->execute($ordersCaptureRequest);
            
            if ($response->result->status === 'COMPLETED') {
                // Create or update subscription
                $user = Auth::user();
                
                // Check if user has an active subscription
                $activeSubscription = $user->subscriptions()
                    ->where('status', 'active')
                    ->first();
                
                if ($activeSubscription) {
                    // Update existing subscription
                    $activeSubscription->update([
                        'plan_id' => $plan->id,
                        'starts_at' => now(),
                        'ends_at' => now()->addDays($plan->duration_in_days),
                        'payment_id' => $response->result->id,
                        'payment_method' => 'paypal',
                        'status' => 'active',
                    ]);
                } else {
                    // Create new subscription
                    Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'starts_at' => now(),
                        'ends_at' => now()->addDays($plan->duration_in_days),
                        'payment_id' => $response->result->id,
                        'payment_method' => 'paypal',
                        'status' => 'active',
                    ]);
                }

                return redirect()->route('manager.dashboard')
                    ->with('success', 'Subscription activated successfully!');
            }

            return redirect()->route('subscription.plans')
                ->with('error', 'Payment failed. Please try again.');
        } catch (\Exception $e) {
            return redirect()->route('subscription.plans')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the user's subscription history.
     */
    public function history()
    {
        $subscriptions = Auth::user()->subscriptions()->with('plan')->latest()->paginate(10);
        
        return view('subscriptions.history', compact('subscriptions'));
    }

    /**
     * Cancel the user's active subscription.
     */
    public function cancel()
    {
        $user = Auth::user();
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();
        
        if ($activeSubscription) {
            $activeSubscription->markAsCancelled();
            
            return redirect()->route('subscription.history')
                ->with('success', 'Subscription cancelled successfully.');
        }

        return redirect()->route('subscription.history')
            ->with('error', 'No active subscription found.');
    }

    /**
     * Get the PayPal client.
     */
    private function getPayPalClient()
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');
        
        $environment = new SandboxEnvironment($clientId, $clientSecret);
        
        return new PayPalHttpClient($environment);
    }

    /**
     * Show the form for granting a subscription to a user.
     */
    public function showGrantForm(User $user)
    {
        // Only allow granting subscriptions to property managers
        if (!$user->isPropertyManager()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Subscriptions can only be granted to property managers.');
        }

        $plans = SubscriptionPlan::where('is_active', true)->get();
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        return view('subscriptions.grant', compact('user', 'plans', 'activeSubscription'));
    }

    /**
     * Grant a subscription to a user.
     */
    public function grantSubscription(Request $request, User $user)
    {
        // Validate request
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:days,months,years',
        ]);

        // Only allow granting subscriptions to property managers
        if (!$user->isPropertyManager()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Subscriptions can only be granted to property managers.');
        }

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Calculate the end date based on duration and unit
        $durationInDays = match ($validated['duration_unit']) {
            'days' => $validated['duration'],
            'months' => $validated['duration'] * 30,
            'years' => $validated['duration'] * 365,
        };

        // Check if user has an active subscription
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();

        if ($activeSubscription) {
            // Update existing subscription
            $activeSubscription->update([
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($durationInDays),
                'payment_method' => 'manual',
                'status' => 'active',
            ]);
        } else {
            // Create new subscription
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($durationInDays),
                'payment_method' => 'manual',
                'status' => 'active',
            ]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Subscription granted successfully.');
    }

    /**
     * Display a listing of subscription plans.
     */
    public function plans()
    {
        $plans = SubscriptionPlan::all();
        return view('subscriptions.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new subscription plan.
     */
    public function create()
    {
        return view('subscriptions.plans.create');
    }

    /**
     * Store a newly created subscription plan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'property_limit' => 'required|integer|min:1',
            'description' => 'required|string',
        ]);

        SubscriptionPlan::create($request->all());

        return redirect()->route('admin.subscription.plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Show the form for editing the specified subscription plan.
     */
    public function edit(SubscriptionPlan $plan)
    {
        return view('subscriptions.plans.edit', compact('plan'));
    }

    /**
     * Update the specified subscription plan in storage.
     */
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'property_limit' => 'required|integer|min:1',
            'description' => 'required|string',
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.subscription.plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove the specified subscription plan from storage.
     */
    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.subscription.plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }
} 