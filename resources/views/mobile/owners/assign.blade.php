@extends('mobile.layout')

@section('title', 'Assign Owner')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div class="mb-2 flex items-center">
            <a href="{{ route('mobile.owners.show', $owner->id) }}" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        
        <h2 class="text-center text-lg md:text-xl lg:text-2xl font-bold mb-4">Assign Owner: {{ $owner->displayName }}</h2>
        
        <form method="POST" action="{{ route('mobile.owners.assign.update', $owner->id) }}">
            @csrf
            
            <!-- Current Assignment Display -->
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-semibold text-blue-800 mb-2">Current Assignment</h3>
                @if($owner->assignedTeamMembers->count() > 0)
                    <p class="text-blue-700 mb-2">Currently assigned to:</p>
                    <ul class="text-blue-700 text-sm space-y-1">
                        @foreach($owner->assignedTeamMembers as $assignment)
                            <li>• <strong>{{ $assignment->user->name }}</strong> ({{ $assignment->user->role->name }})</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">No team members assigned</p>
                @endif
            </div>
            
            <!-- Team Member Assignment Section -->
            <div class="mb-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                <label class="block font-semibold mb-2 text-purple-800">Assign to Team Members</label>
                <p class="text-purple-600 text-xs mb-3">Select team members to manage this owner and all their properties:</p>
                
                <div class="space-y-2">
                    @if($teamMembers->count() > 0)
                        @foreach($teamMembers as $member)
                            <label class="flex items-center">
                                <input type="checkbox" name="managed_by[]" value="{{ $member->id }}" 
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                    {{ $owner->assignedTeamMembers->contains('user_id', $member->id) ? 'checked' : '' }}>
                                <span class="ml-2 text-xs text-gray-700">{{ $member->name }}<br><span class="text-gray-500">({{ $member->role->name }})</span></span>
                            </label>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-xs">No team members available for assignment.</p>
                    @endif
                </div>
                
                @error('managed_by')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Properties Affected -->
            @if($owner->properties->count() > 0)
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <h3 class="font-semibold text-green-800 mb-2">Properties Affected</h3>
                <p class="text-green-700 text-xs mb-2">This assignment will affect the following {{ $owner->properties->count() }} properties:</p>
                <ul class="text-green-700 text-xs space-y-1">
                    @foreach($owner->properties as $property)
                        <li>• {{ $property->name }} - {{ $property->address }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Update Assignment
                </button>
                <a href="{{ route('mobile.owners.show', $owner->id) }}" class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition text-sm font-medium text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
