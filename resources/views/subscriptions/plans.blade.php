@extends('layouts.app')

@section('title', 'Subscription Plans')
@section('header', 'Subscription Plans')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Choose Your Subscription Plan</h2>
        <p class="text-gray-600 mt-2">Select the plan that best fits your property management needs.</p>
    </div>
    
    @if($activeSubscription)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        You currently have an active subscription to the <strong>{{ $activeSubscription->plan->name }}</strong> plan, which expires on <strong>{{ $activeSubscription->ends_at->format('F j, Y') }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        @foreach($plans as $plan)
            @if(strpos($plan->name, 'Annual') === false)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 @if($plan->name == 'Standard') border-blue-500 @endif">
                    <div class="p-6 @if($plan->name == 'Standard') bg-blue-50 @endif">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                        
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-gray-800">{{ $plan->formatted_price }}</span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-700">{{ $plan->property_limit }} Properties</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-700">{{ $plan->technician_limit }} Technicians</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-700">Unlimited Maintenance Requests</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-700">Email Notifications</span>
                            </li>
                        </ul>
                        
                        <a href="{{ route('subscription.checkout', $plan) }}" class="block w-full text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white @if($plan->name == 'Standard') bg-blue-600 hover:bg-blue-700 @else bg-gray-600 hover:bg-gray-700 @endif focus:outline-none focus:ring-2 focus:ring-offset-2 @if($plan->name == 'Standard') focus:ring-blue-500 @else focus:ring-gray-500 @endif">
                            @if($activeSubscription && $activeSubscription->plan_id == $plan->id)
                                Current Plan
                            @else
                                Select Plan
                            @endif
                        </a>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-8">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Annual Plans</h3>
            <p class="text-gray-600 mb-6">Save up to 20% with our annual subscription plans.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    @if(strpos($plan->name, 'Annual') !== false)
                        <div class="border border-gray-200 rounded-lg p-4 @if($plan->name == 'Annual Standard') bg-blue-50 border-blue-500 @endif">
                            <h4 class="text-lg font-bold text-gray-800 mb-2">{{ str_replace('Annual ', '', $plan->name) }}</h4>
                            
                            <div class="mb-4">
                                <span class="text-2xl font-bold text-gray-800">{{ $plan->formatted_price }}</span>
                                <span class="text-gray-600">/year</span>
                            </div>
                            
                            <ul class="space-y-1 mb-4 text-sm">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span class="text-gray-700">{{ $plan->property_limit }} Properties</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span class="text-gray-700">{{ $plan->technician_limit }} Technicians</span>
                                </li>
                            </ul>
                            
                            <a href="{{ route('subscription.checkout', $plan) }}" class="block w-full text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white @if($plan->name == 'Annual Standard') bg-blue-600 hover:bg-blue-700 @else bg-gray-600 hover:bg-gray-700 @endif focus:outline-none focus:ring-2 focus:ring-offset-2 @if($plan->name == 'Annual Standard') focus:ring-blue-500 @else focus:ring-gray-500 @endif">
                                @if($activeSubscription && $activeSubscription->plan_id == $plan->id)
                                    Current Plan
                                @else
                                    Select Plan
                                @endif
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">All Plans Include</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-qrcode"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">QR Code Generation</h4>
                        <p class="mt-1 text-sm text-gray-600">Generate QR codes for easy maintenance request submission.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Email Notifications</h4>
                        <p class="mt-1 text-sm text-gray-600">Receive notifications for new requests and updates.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Image Uploads</h4>
                        <p class="mt-1 text-sm text-gray-600">Allow requesters to upload images with their maintenance requests.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Task Management</h4>
                        <p class="mt-1 text-sm text-gray-600">Assign tasks to technicians and track progress.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Dashboard Analytics</h4>
                        <p class="mt-1 text-sm text-gray-600">View statistics and track maintenance request status.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Comment System</h4>
                        <p class="mt-1 text-sm text-gray-600">Add comments and updates to maintenance requests.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($activeSubscription)
        <div class="mt-8 text-center">
            <form method="POST" action="{{ route('subscription.cancel') }}" onsubmit="return confirm('Are you sure you want to cancel your subscription? You will still have access until the end of your billing period.');">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                    Cancel Subscription
                </button>
            </form>
            
            <p class="text-sm text-gray-600 mt-2">
                You can view your subscription history <a href="{{ route('subscription.history') }}" class="text-blue-600 hover:text-blue-800">here</a>.
            </p>
        </div>
    @endif
</div>
@endsection 