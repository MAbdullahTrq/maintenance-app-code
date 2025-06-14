@extends('mobile.layout')

@section('title', 'Subscription Plans')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Choose Your Subscription Plan</h2>
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
    <div class="grid grid-cols-1 gap-6 mb-8">
        @foreach($plans as $plan)
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                    <div class="mb-4">
                        <span class="text-2xl font-bold text-gray-800">{{ $plan->formatted_price }}</span>
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
                    <a href="{{ route('subscription.checkout', $plan) }}" class="block w-full text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        @if($activeSubscription && $activeSubscription->plan_id == $plan->id)
                            Current Plan
                        @else
                            Select Plan
                        @endif
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">All Plans Include</h3>
            <ul class="space-y-2">
                <li class="flex items-center">
                    <i class="fas fa-qrcode text-blue-500 mr-2"></i>
                    <span class="text-gray-700">QR Code Generation</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-bell text-blue-500 mr-2"></i>
                    <span class="text-gray-700">Email Notifications</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-image text-blue-500 mr-2"></i>
                    <span class="text-gray-700">Image Uploads</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-tasks text-blue-500 mr-2"></i>
                    <span class="text-gray-700">Task Management</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                    <span class="text-gray-700">Dashboard Analytics</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-comments text-blue-500 mr-2"></i>
                    <span class="text-gray-700">Comment System</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection 