@extends('layouts.app')

@section('title', 'Create Maintenance Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('maintenance.index') }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Maintenance Requests
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-3xl mx-auto">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-gray-900">Create Maintenance Request</h1>
        </div>
        
        <div class="p-6">
            <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data" id="maintenance-form">
                @csrf
                
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., Broken Sink">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Describe the issue in detail...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., Kitchen, Bathroom, Living Room">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select id="priority" name="priority" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                    <select id="property_id" name="property_id" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a property</option>
                        @foreach(auth()->user()->managedProperties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="checklist_id" class="block text-sm font-medium text-gray-700 mb-1">Use Checklist (Optional)</label>
                    <select id="checklist_id" name="checklist_id" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">No checklist</option>
                        @foreach(auth()->user()->checklists as $checklist)
                            <option value="{{ $checklist->id }}" {{ old('checklist_id') == $checklist->id ? 'selected' : '' }}>
                                {{ $checklist->name }} ({{ $checklist->items->count() }} items)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-gray-500 text-xs mt-1">Select a checklist to add structured items to this request.</p>
                    @error('checklist_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Images (Optional)</label>
                    <input type="file" id="images" name="images[]" multiple 
                        class="w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-gray-500 text-xs mt-1">Upload images of the issue. Maximum 5 images. Supported formats: JPG, PNG.</p>
                    <div id="image-previews" class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-3 hidden"></div>
                    @error('images.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Create Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Image resize and preview functionality
const input = document.getElementById('images');
const form = document.getElementById('maintenance-form');
const previewContainer = document.getElementById('image-previews');

input.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    if (!files.length) {
        previewContainer.classList.add('hidden');
        return;
    }
    
    previewContainer.innerHTML = '';
    previewContainer.classList.remove('hidden');
    
    const resizedFiles = [];
    let processedCount = 0;
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                const MAX_SIZE = 600;
                let width = img.width;
                let height = img.height;
                
                // Calculate new dimensions
                if (width > height) {
                    if (width > MAX_SIZE) {
                        height *= MAX_SIZE / width;
                        width = MAX_SIZE;
                    }
                } else {
                    if (height > MAX_SIZE) {
                        width *= MAX_SIZE / height;
                        height = MAX_SIZE;
                    }
                }
                
                // Create canvas and resize
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                canvas.toBlob(function(blob) {
                    const resizedFile = new File([blob], file.name, {type: blob.type});
                    resizedFiles[index] = resizedFile;
                    processedCount++;
                    
                    // Show preview
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative';
                    const previewImg = document.createElement('img');
                    previewImg.src = canvas.toDataURL(file.type, 0.85);
                    previewImg.className = 'w-full h-24 object-cover rounded border shadow-sm';
                    previewDiv.appendChild(previewImg);
                    
                    // Add file name
                    const fileName = document.createElement('p');
                    fileName.textContent = file.name;
                    fileName.className = 'text-xs text-gray-600 mt-1 truncate';
                    previewDiv.appendChild(fileName);
                    
                    previewContainer.appendChild(previewDiv);
                    
                    // Update input files when all images are processed
                    if (processedCount === files.length) {
                        const dataTransfer = new DataTransfer();
                        resizedFiles.forEach(file => {
                            if (file) dataTransfer.items.add(file);
                        });
                        input.files = dataTransfer.files;
                    }
                }, file.type, 0.85);
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection 