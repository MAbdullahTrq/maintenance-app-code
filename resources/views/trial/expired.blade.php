@extends('layouts.app')

@section('title', 'Trial Expired')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Trial Expired</h2>
            <p class="mt-2 text-sm text-gray-600">
                Your free trial has ended and your account has been locked.
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>

                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Locked</h3>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-3">
                        Your account has been locked due to trial expiration. Here's what happens next:
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2 text-left">
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            Your data will be kept for 90 days from trial expiration
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            You'll receive reminder emails to reactivate your account
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            After 90 days, your account and data will be permanently deleted
                        </li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <!-- Reactivate Button -->
                    <a href="{{ route('trial.reactivate') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reactivate Account
                    </a>

                    <!-- Subscribe Button -->
                    <a href="{{ route('subscription.plans') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Subscribe Now
                    </a>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        Need help? Contact us at 
                        <a href="mailto:support@maintainxtra.com" class="text-blue-600 hover:text-blue-500">
                            support@maintainxtra.com
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 