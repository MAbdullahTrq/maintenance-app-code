@extends('layouts.app')

@section('title', 'Team Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Team Management</h1>
                <p class="text-gray-600 mt-2">Manage your team assistants and workspace members</p>
            </div>
            <a href="{{ route('team.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                <i class="fas fa-plus mr-2"></i>Invite Team Member
            </a>
        </div>

        <!-- Workspace Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-blue-900 mb-2">Workspace Information</h2>
            <p class="text-blue-700">
                <strong>Workspace Owner:</strong> {{ $workspaceOwner->name }} ({{ $workspaceOwner->email }})
            </p>
            <p class="text-blue-700">
                <strong>Team Assistants:</strong> {{ $teamMembers->count() }} members
            </p>
            <p class="text-blue-700 text-sm mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                Team members are assistants who help you manage properties and requests. Technicians are managed separately.
            </p>
        </div>

        <!-- Team Members -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Team Assistants</h2>
                <p class="text-sm text-gray-600 mt-1">These are your workspace assistants who can help manage properties and requests</p>
            </div>
            
            @if($teamMembers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($teamMembers as $member)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($member->image)
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $member->image) }}" alt="{{ $member->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-gray-600 font-medium">{{ substr($member->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $member->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" action="{{ route('team.update-role', $member->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role_id" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                @foreach($availableRoles as $role)
                                                    <option value="{{ $role->id }}" {{ $member->role_id == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->created_at->format('M j, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('team.remove-member', $member->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to remove this team member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <div class="text-gray-500 text-lg mb-2">No team members yet</div>
                    <p class="text-gray-400">Invite team members to get started</p>
                </div>
            @endif
        </div>

        <!-- Pending Invitations -->
        @if($pendingInvitations->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Pending Invitations</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingInvitations as $invitation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invitation->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $invitation->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invitation->role->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invitation->created_at->format('M j, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($invitation->isExpired())
                                            <span class="text-red-600">Expired</span>
                                        @else
                                            {{ $invitation->expires_at->format('M j, Y') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('team.cancel-invitation', $invitation->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this invitation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 