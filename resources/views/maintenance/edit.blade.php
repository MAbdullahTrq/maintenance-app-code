@extends('layouts.app')

@section('title', 'Edit Maintenance Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('maintenance.show', $maintenance) }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Request
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-gray-900">Edit Maintenance Request</h1>
        </div>
        
        <div class="p-6">
            <form action="{{ route('maintenance.update', $maintenance) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $maintenance->title) }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., Broken Sink">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                    <select name="property_id" id="property_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Property --</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id', $maintenance->property_id) == $property->id ? 'selected' : '' }}>
                                {{ $property->name }} - {{ $property->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $maintenance->location) }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., Kitchen, Unit 101">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative">
                            <input type="radio" id="priority-low" name="priority" value="low" class="absolute opacity-0" {{ old('priority', $maintenance->priority) == 'low' ? 'checked' : '' }}>
                            <label for="priority-low" class="block cursor-pointer">
                                <div class="font-medium text-blue-600 mb-1">LOW</div>
                                <div class="text-sm text-gray-500">You can fix after we leave, just wanted to let you know.</div>
                            </label>
                        </div>
                        
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative">
                            <input type="radio" id="priority-medium" name="priority" value="medium" class="absolute opacity-0" {{ old('priority', $maintenance->priority) == 'medium' ? 'checked' : '' }}>
                            <label for="priority-medium" class="block cursor-pointer">
                                <div class="font-medium text-yellow-600 mb-1">MEDIUM</div>
                                <div class="text-sm text-gray-500">You can fix the next cleaning day is fine.</div>
                            </label>
                        </div>
                        
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative">
                            <input type="radio" id="priority-high" name="priority" value="high" class="absolute opacity-0" {{ old('priority', $maintenance->priority) == 'high' ? 'checked' : '' }}>
                            <label for="priority-high" class="block cursor-pointer">
                                <div class="font-medium text-red-600 mb-1">HIGH</div>
                                <div class="text-sm text-gray-500">Fix asap please.</div>
                            </label>
                        </div>
                    </div>
                    @error('priority')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Describe the issue in detail...">{{ old('description', $maintenance->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Add More Images (Optional)</label>
                    <input type="file" id="images" name="images[]" multiple 
                        class="w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-gray-500 text-xs mt-1">Upload images of the issue. Maximum 5 images. Supported formats: JPG, PNG.</p>
                    @error('images.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                @if($maintenance->images && $maintenance->images->where('type', 'initial')->count() > 0)
                    <div class="mb-6">
                        <h3 class="block text-sm font-medium text-gray-700 mb-3">Current Images</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($maintenance->images->where('type', 'initial') as $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Maintenance Image" class="w-full h-40 object-cover rounded-lg">
                                    
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <form action="{{ route('maintenance.image.delete', $image) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg" onclick="return confirm('Are you sure you want to delete this image?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('maintenance.show', $maintenance) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Update Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 