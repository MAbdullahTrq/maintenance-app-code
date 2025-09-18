@extends('mobile.layout')

@section('title', 'Assign Team Members')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="mb-4">
            <a href="{{ route('mobile.properties.show', $property->id) }}" class="text-blue-500 hover:text-blue-700 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back to Property
            </a>
        </div>
        
        <h2 class="text-center text-lg font-bold mb-4">Assign Team Members</h2>
        
        <!-- Property Info -->
        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <div class="flex items-center gap-3">
                @if($property->image)
                    <img src="{{ asset('storage/' . $property->image) }}" class="rounded-full w-12 h-12 object-cover" alt="Property Image">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($property->name) }}&background=eee&color=555&size=48" class="rounded-full w-12 h-12" alt="Property">
                @endif
                <div>
                    <div class="font-semibold text-gray-900">{{ $property->name }}</div>
                    <div class="text-sm text-gray-600">{{ $property->address }}</div>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('mobile.properties.assign.update', $property->id) }}">
            @csrf
            
            <!-- Team Member Assignment Section -->
            <div class="mb-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                <label class="block font-semibold mb-2 text-purple-800">Assign Team Members</label>
                <p class="text-purple-600 text-xs mb-3">Select team members who should receive email updates for requests related to this property:</p>
                <div class="space-y-2">
                    @if($editorTeamMembers->count() > 0)
                        @foreach($editorTeamMembers as $member)
                            <label class="flex items-center">
                                <input type="checkbox" name="assigned_team_members[]" value="{{ $member->id }}" 
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                    {{ $property->assignedTeamMembers->contains('user_id', $member->id) ? 'checked' : '' }}>
                                <span class="ml-2 text-xs text-gray-700">{{ $member->name }}<br><span class="text-gray-500">({{ $member->email }})</span></span>
                            </label>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-xs">No editor team members available for assignment.</p>
                    @endif
                </div>
                @error('assigned_team_members')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Current Assignments Display -->
            @if($property->assignedTeamMembers->count() > 0)
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <label class="block font-semibold mb-2 text-green-800">Currently Assigned</label>
                <div class="space-y-1">
                    @foreach($property->assignedTeamMembers as $assignment)
                        <div class="flex items-center text-sm text-green-700">
                            <i class="fas fa-user-check mr-2 text-green-600"></i>
                            {{ $assignment->user->name }}
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="flex gap-2">
                <a href="{{ route('mobile.properties.show', $property->id) }}" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded text-center hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition-colors">
                    Update Assignments
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
