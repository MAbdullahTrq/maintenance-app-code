@extends('layouts.app')

@section('title', 'Trial Status')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Trial Status</h2>
                <p class="mt-2 text-gray-600">Manage your free trial and subscription</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Trial Status Card -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Status</h3>
                
                @if($user->isOnTrial())
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-blue-900">Free Trial Active</h4>
                                <p class="text-blue-700">
                                    {{ $user->getTrialDaysLeft() }} days remaining in your free trial
                                </p>
                                <p class="text-sm text-blue-600 mt-1">
                                    Started: {{ $user->trial_started_at->format('M j, Y') }} | 
                                    Expires: {{ $user->trial_expires_at->format('M j, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($user->isInGracePeriod())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-yellow-900">Grace Period</h4>
                                <p class="text-yellow-700">
                                    Your trial has ended. {{ $user->getGracePeriodDaysLeft() }} days left in grace period.
                                </p>
                                <p class="text-sm text-yellow-600 mt-1">
                                    Subscribe now to keep your data and continue using the platform.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($user->isAccountLocked())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-red-900">Account Locked</h4>
                                <p class="text-red-700">
                                    Your account has been locked due to trial expiration.
                                </p>
                                <p class="text-sm text-red-600 mt-1">
                                    Data deletion scheduled for: {{ $user->data_deletion_at->format('M j, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($user->hasActiveSubscription())
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-green-900">Active Subscription</h4>
                                <p class="text-green-700">
                                    You have an active subscription. Full access granted.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($user->isOnTrial())
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Extend Trial</h4>
                        <p class="text-gray-600 mb-4">
                            Need more time? You can extend your trial by 7 days (one-time only).
                        </p>
                        @if(!$user->trial_extended)
                            <form method="POST" action="{{ route('trial.extend') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Extend Trial
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500">Trial already extended</p>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Subscribe Now</h4>
                        <p class="text-gray-600 mb-4">
                            Upgrade to a paid plan to continue using all features after your trial ends.
                        </p>
                        <a href="{{ route('subscription.plans') }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 inline-block text-center">
                            View Plans
                        </a>
                    </div>
                @elseif($user->isInGracePeriod())
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Subscribe Now</h4>
                        <p class="text-gray-600 mb-4">
                            Your trial has ended. Subscribe now to keep your data and continue using the platform.
                        </p>
                        <a href="{{ route('subscription.plans') }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-block text-center">
                            Subscribe Now
                        </a>
                    </div>
                @elseif($user->isAccountLocked())
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Reactivate Account</h4>
                        <p class="text-gray-600 mb-4">
                            Reactivate your account to start a new 30-day trial and recover your data.
                        </p>
                        <a href="{{ route('trial.reactivate') }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-block text-center">
                            Reactivate Account
                        </a>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Subscribe</h4>
                        <p class="text-gray-600 mb-4">
                            Subscribe to a paid plan to unlock your account and continue using the platform.
                        </p>
                        <a href="{{ route('subscription.plans') }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 inline-block text-center">
                            View Plans
                        </a>
                    </div>
                @endif
            </div>

            <!-- Trial Information -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Trial Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">What's Included</h5>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Full access to all features</li>
                            <li>• Unlimited maintenance requests</li>
                            <li>• Property and owner management</li>
                            <li>• Technician assignment</li>
                            <li>• Email notifications</li>
                            <li>• Mobile app access</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">After Trial</h5>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• 7-day grace period</li>
                            <li>• Data kept for 90 days</li>
                            <li>• Reminder emails sent</li>
                            <li>• Easy reactivation</li>
                            <li>• No credit card required</li>
                            <li>• Cancel anytime</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 