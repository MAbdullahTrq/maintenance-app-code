@extends('layouts.app')

@section('title', 'Grant Subscription')
@section('header', 'Grant Subscription')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('admin.users.index') }}" class="text-gray-700 hover:text-blue-600">Users</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('admin.users.show', $user) }}" class="text-gray-700 hover:text-blue-600">{{ $user->name }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Grant Subscription</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-gift text-blue-500 mr-2"></i>Grant Subscription to {{ $user->name }}
                </h2>

                @if($activeSubscription)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    This user already has an active subscription ({{ $activeSubscription->plan->name }}) that expires on {{ $activeSubscription->ends_at->format('F j, Y') }}.
                                    Granting a new subscription will override the existing one.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.users.grant-subscription.store', $user) }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Subscription Plan -->
                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group text-gray-400 mr-2"></i>Subscription Plan
                            </label>
                            <select id="plan_id" name="plan_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('plan_id') border-red-500 @enderror" required>
                                <option value="">Select a plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} - {{ $plan->formatted_price }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-clock text-gray-400 mr-2"></i>Duration
                                </label>
                                <input type="number" id="duration" name="duration" value="{{ old('duration', 1) }}" min="1" class="mt-1 block w-full border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('duration') border-red-500 @enderror" required>
                                @error('duration')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration_unit" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar text-gray-400 mr-2"></i>Duration Unit
                                </label>
                                <select id="duration_unit" name="duration_unit" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('duration_unit') border-red-500 @enderror" required>
                                    <option value="days" {{ old('duration_unit') == 'days' ? 'selected' : '' }}>Days</option>
                                    <option value="months" {{ old('duration_unit') == 'months' ? 'selected' : '' }}>Months</option>
                                    <option value="years" {{ old('duration_unit', 'years') == 'years' ? 'selected' : '' }}>Years</option>
                                </select>
                                @error('duration_unit')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-check mr-2"></i>Grant Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 