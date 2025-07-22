@extends('mobile.layout')

@section('title', 'Invite Team Member')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('team.index') }}" class="text-blue-600 hover:text-blue-800 mr-3">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Invite Team Member</h1>
        </div>
        <p class="text-gray-600">Send an invitation to join your workspace</p>
    </div>

    <!-- Invitation Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('team.store') }}">
            @csrf
            
            <!-- Name Field -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="Enter full name"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Field -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                       placeholder="Enter email address"
                       required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selection -->
            <div class="mb-6">
                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select id="role_id" 
                        name="role_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role_id') border-red-500 @enderror"
                        required>
                    <option value="">Select a role</option>
                    @foreach($availableRoles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <!-- Role Descriptions -->
                <div class="mt-3 space-y-2">
                    @foreach($availableRoles as $role)
                        <div class="text-xs text-gray-600 p-2 bg-gray-50 rounded">
                            <strong>{{ $role->name }}:</strong> {{ $role->description ?? 'No description available' }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Personal Message (Optional) -->
            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Personal Message (Optional)</label>
                <textarea id="message" 
                          name="message" 
                          rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('message') border-red-500 @enderror"
                          placeholder="Add a personal message to your invitation...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex space-x-3">
                <a href="{{ route('team.index') }}" 
                   class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-center hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Send Invitation
                </button>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">What happens next?</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• An invitation email will be sent to the provided email address</li>
            <li>• The recipient will receive a secure link to join your workspace</li>
            <li>• They'll be able to create their account and set their password</li>
            <li>• The invitation expires after 7 days for security</li>
        </ul>
    </div>
</div>
@endsection 