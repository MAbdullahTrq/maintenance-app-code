@extends('layouts.app')

@section('title', 'Invite Team Member')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Invite Team Member</h1>
            <p class="text-gray-600 mt-2">Send an invitation to join your team</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" action="{{ route('team.store') }}">
                @csrf

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div class="mb-6">
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role_id" id="role_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Descriptions -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Role Permissions:</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div>
                            <strong>Team Member:</strong> View and manage properties, handle maintenance requests, access reports and team data
                        </div>
                        <div>
                            <strong>Viewer:</strong> View properties and maintenance requests, access reports and analytics
                        </div>
                        <div>
                            <strong>Editor:</strong> View and edit properties and maintenance requests, create and manage reports, access all team data
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('team.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-900 mb-2">What happens next?</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• An invitation email will be sent to the provided email address</li>
                <li>• The invitation will expire in 7 days</li>
                <li>• When they accept, they'll create their account and join your team</li>
                <li>• They'll have access based on the role you've assigned</li>
            </ul>
        </div>
    </div>
</div>
@endsection 