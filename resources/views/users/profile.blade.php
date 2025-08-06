@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb Navigation -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Dashboard
                    </a>
                @elseif(auth()->user()->isPropertyManager())
                    <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Dashboard
                    </a>
                @elseif(auth()->user()->isTechnician())
                    <a href="{{ route('technician.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Dashboard
                    </a>
                @endif
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">My Profile</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Information Card -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 px-6 py-4">
                    <h1 class="text-xl font-semibold text-white">Profile Information</h1>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone (Optional)</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Account Information Card -->
        <div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gray-800 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Account Information</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Role</p>
                            <p class="text-base text-gray-900">{{ $user->role->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Member Since</p>
                            <p class="text-base text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                        
                        <!-- Subscription Information -->
                        <div class="border-t pt-4 mt-4">
                            <p class="text-sm font-medium text-gray-500 mb-2">Subscription</p>
                            @if($user->isOnTrial())
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-blue-800">Free Trial</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Active
                                        </span>
                                    </div>
                                                                            <div class="text-sm text-blue-700">
                                            <p class="mb-1">Started: {{ $user->trial_started_at ? $user->trial_started_at->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
                                            @if($user->trial_expires_at)
                                                <p class="font-medium">
                                                    @php
                                                        $daysLeft = now()->diffInDays($user->trial_expires_at, false);
                                                    @endphp
                                                    @if($daysLeft > 0)
                                                        Expires on {{ $user->trial_expires_at->format('M d, Y \a\t g:i A') }}
                                                    @elseif($daysLeft == 0)
                                                        Expires today at {{ $user->trial_expires_at->format('g:i A') }}
                                                    @else
                                                        Expired on {{ $user->trial_expires_at->format('M d, Y \a\t g:i A') }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                </div>
                            @elseif($user->hasActiveSubscription())
                                @php
                                    $activeSubscription = $user->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->first();
                                @endphp
                                @if($activeSubscription)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-green-800">{{ $activeSubscription->plan->name ?? 'Active Plan' }}</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </div>
                                        <div class="text-sm text-green-700">
                                            <p class="mb-1">Started: {{ $activeSubscription->created_at->format('M d, Y \a\t g:i A') }}</p>
                                            <p class="mb-1">Next billing: {{ $activeSubscription->ends_at->format('M d, Y \a\t g:i A') }}</p>
                                            @if($activeSubscription->plan)
                                                <p class="font-medium">€{{ number_format($activeSubscription->plan->price, 2) }}/month</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <div class="text-sm text-gray-600">
                                            <p>No active subscription found.</p>
                                        </div>
                                    </div>
                                @endif
                            @elseif($user->isInGracePeriod())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-yellow-800">Grace Period</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Expired
                                        </span>
                                    </div>
                                                                            <div class="text-sm text-yellow-700">
                                            <p class="mb-1">Trial expired on: {{ $user->trial_expires_at ? $user->trial_expires_at->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
                                            @php
                                                $graceEndDate = $user->trial_expires_at ? $user->trial_expires_at->addDays(7) : null;
                                                $graceDaysLeft = $graceEndDate ? now()->diffInDays($graceEndDate, false) : 0;
                                            @endphp
                                            @if($graceDaysLeft > 0)
                                                <p class="font-medium">Grace period ends on {{ $graceEndDate->format('M d, Y \a\t g:i A') }}</p>
                                            @else
                                                <p class="font-medium">Grace period ended on {{ $graceEndDate ? $graceEndDate->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
                                            @endif
                                        </div>
                                </div>
                            @else
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-red-800">No Active Subscription</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    </div>
                                    <div class="text-sm text-red-700">
                                        <p>Your account is not active. Please subscribe to continue.</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!$user->hasActiveSubscription() && !$user->isOnTrial())
                                <div class="mt-3">
                                    <a href="{{ route('subscription.plans') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Subscribe Now
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Password Change Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-yellow-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Password</h2>
                </div>
                
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Ensure your account is using a long, random password to stay secure.</p>
                    
                    <a href="{{ route('password.change') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 