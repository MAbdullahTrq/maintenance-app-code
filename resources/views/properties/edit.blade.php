@extends('layouts.app')

@section('title', 'Edit Property')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb Navigation -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('properties.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Properties
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('properties.show', $property) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{ $property->name }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Property</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">Edit Property</h1>
        
        <form action="{{ route('properties.update', $property) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Property Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $property->name) }}" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                    required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-medium mb-2">Property Address</label>
                <textarea name="address" id="address" rows="3" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror" 
                    required>{{ old('address', $property->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="owner_id" class="block text-gray-700 font-medium mb-2">Property Owner</label>
                <select name="owner_id" id="owner_id" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('owner_id') border-red-500 @enderror" 
                    required>
                    <option value="">Select an owner</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ old('owner_id', $property->owner_id) == $owner->id ? 'selected' : '' }}>
                            {{ $owner->displayName }}
                        </option>
                    @endforeach
                </select>
                @error('owner_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @if($owners->count() === 0)
                    <p class="text-yellow-600 text-sm mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        No owners found. <a href="{{ route('owners.create') }}" class="underline hover:text-yellow-800">Create an owner first</a>.
                    </p>
                @endif
            </div>

            @if(Auth::user()->isPropertyManager())
            <!-- Team Member Assignment Section (Managers Only) -->
            <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <label class="block text-purple-800 font-medium mb-3">Assign Team Members</label>
                <p class="text-purple-600 text-sm mb-3">Select team members who should receive email updates for requests related to this property:</p>
                <div class="space-y-2">
                    @if(isset($editorTeamMembers) && $editorTeamMembers->count() > 0)
                        @foreach($editorTeamMembers as $member)
                            <label class="flex items-center">
                                <input type="checkbox" name="assigned_team_members[]" value="{{ $member->id }}" 
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                    {{ in_array($member->id, old('assigned_team_members', $property->assignedTeamMembers->pluck('user_id')->toArray())) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">{{ $member->name }} ({{ $member->email }})</span>
                            </label>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">No editor team members available for assignment.</p>
                    @endif
                </div>
                @error('assigned_team_members')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif
            
            <div class="mb-6">
                <label for="image" class="block text-gray-700 font-medium mb-2">Property Image</label>
                @if($property->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $property->image) }}" alt="{{ $property->name }}" class="w-32 h-32 object-cover rounded">
                        <p class="text-sm text-gray-500 mt-1">Current image</p>
                    </div>
                @endif
                <input type="file" name="image" id="image" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror">
                <p class="text-sm text-gray-500 mt-1">Optional. Upload a new image to replace the current one (JPEG, PNG, JPG, GIF, max 2MB).</p>
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <a href="{{ route('properties.show', $property) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg mr-2 hover:bg-gray-400">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Update Property</button>
            </div>
        </form>
    </div>
</div>
@endsection 