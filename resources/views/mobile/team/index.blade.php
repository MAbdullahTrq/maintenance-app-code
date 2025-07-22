@extends('mobile.layout')

@section('title', 'Team Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Team Management</h1>
            <p class="text-gray-600 mt-1">Manage your team assistants</p>
        </div>
        <a href="{{ route('mobile.team.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-plus mr-1"></i>Invite
        </a>
    </div>

    <!-- Workspace Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold text-blue-900 mb-2">Workspace Information</h2>
        <div class="text-blue-700 text-sm space-y-1">
            <p><strong>Workspace Owner:</strong> {{ $workspaceOwner->name }}</p>
            <p><strong>Team Assistants:</strong> {{ $teamMembers->count() }} members</p>
            <p class="text-xs mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                Team assistants help manage properties and requests. Technicians are managed separately.
            </p>
        </div>
    </div>

    <!-- Team Members -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Team Assistants</h2>
            <p class="text-xs text-gray-600 mt-1">Workspace assistants who help manage properties and requests</p>
        </div>
        
        @if($teamMembers->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($teamMembers as $member)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
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
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                    <div class="text-xs text-gray-400">Joined {{ $member->created_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <select onchange="updateRole({{ $member->id }}, this.value)" class="text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($availableRoles as $role)
                                        <option value="{{ $role->id }}" {{ $member->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button onclick="removeMember({{ $member->id }})" class="text-red-600 hover:text-red-900 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center">
                <div class="text-gray-500 text-lg mb-2">No team members yet</div>
                <p class="text-gray-400 text-sm">Invite team members to get started</p>
            </div>
        @endif
    </div>

    <!-- Pending Invitations -->
    @if($pendingInvitations->count() > 0)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Pending Invitations</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($pendingInvitations as $invitation)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $invitation->name }}</div>
                                <div class="text-sm text-gray-500">{{ $invitation->email }}</div>
                                <div class="text-xs text-gray-400">
                                    Role: {{ $invitation->role->name }} • Sent {{ $invitation->created_at->format('M j, Y') }}
                                </div>
                                @if($invitation->isExpired())
                                    <div class="text-xs text-red-600">Expired</div>
                                @else
                                    <div class="text-xs text-gray-400">Expires {{ $invitation->expires_at->format('M j, Y') }}</div>
                                @endif
                            </div>
                            <button onclick="cancelInvitation({{ $invitation->id }})" class="text-red-600 hover:text-red-900 text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
function updateRole(memberId, roleId) {
    if (confirm('Are you sure you want to update this member\'s role?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/m/team/member/${memberId}/role`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        form.appendChild(methodField);
        
        const roleField = document.createElement('input');
        roleField.type = 'hidden';
        roleField.name = 'role_id';
        roleField.value = roleId;
        form.appendChild(roleField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function removeMember(memberId) {
    if (confirm('Are you sure you want to remove this team member?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/m/team/member/${memberId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function cancelInvitation(invitationId) {
    if (confirm('Are you sure you want to cancel this invitation?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/m/team/invitation/${invitationId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection 