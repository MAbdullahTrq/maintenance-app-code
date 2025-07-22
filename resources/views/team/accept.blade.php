@extends('layouts.guest')

@section('title', 'Accept Team Invitation')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Accept Team Invitation
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Join {{ $invitation->invitedBy->name }}'s team on {{ config('app.name') }}
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow rounded-lg">
            <!-- Invitation Details -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-900 mb-2">Invitation Details</h3>
                <div class="text-sm text-blue-700 space-y-1">
                    <p><strong>From:</strong> {{ $invitation->invitedBy->name }}</p>
                    <p><strong>Role:</strong> {{ $invitation->role->name }}</p>
                    <p><strong>Email:</strong> {{ $invitation->email }}</p>
                </div>
            </div>

            <!-- Role Description -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Role Permissions</h3>
                <div class="text-sm text-gray-600">
                    @if($invitation->role->slug === 'viewer')
                        <ul class="list-disc list-inside space-y-1">
                            <li>View properties and maintenance requests</li>
                            <li>Access reports and analytics</li>
                        </ul>
                    @elseif($invitation->role->slug === 'editor')
                        <ul class="list-disc list-inside space-y-1">
                            <li>View and edit properties and maintenance requests</li>
                            <li>Create and manage reports</li>
                            <li>Access all team data</li>
                        </ul>
                    @else
                        <ul class="list-disc list-inside space-y-1">
                            <li>View and manage properties</li>
                            <li>Handle maintenance requests</li>
                            <li>Access reports and team data</li>
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('team.process-invitation', $invitation->token) }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $invitation->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Create a secure password">
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Confirm your password">
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Accept Invitation & Create Account
                    </button>
                </div>
            </form>

            <!-- Info -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    By accepting this invitation, you agree to join the team and will have access based on your assigned role.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection 