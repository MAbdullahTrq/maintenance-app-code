@extends('mobile.layout')

@section('title', 'Submit Request')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div>
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg md:text-xl lg:text-2xl">Submit Maintenance Request</div>
            </div>
            
            @if($ownerId)
                @php
                    $owner = \App\Models\Owner::find($ownerId);
                @endphp
                @if($owner)
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-blue-800 text-sm">
                            <i class="fas fa-info-circle mr-1"></i>
                            Submitting request for <strong>{{ $owner->displayName }}</strong>
                        </p>
                    </div>
                @endif
            @endif
            
            @if($properties->count() == 0)
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800 text-sm">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        No properties available for this owner. Please add properties first.
                    </p>
                    <a href="{{ route('mobile.properties.create') }}" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        Add Property
                    </a>
                </div>
            @else
            <form method="POST" action="{{ route('mobile.owner.requests.store') }}" enctype="multipart/form-data" id="owner-request-form">
                @csrf
                
                <!-- Property Selection -->
                <div class="mb-6">
                    <label for="property_id" class="block text-gray-700 text-sm font-medium mb-2">Select Property <span class="text-red-500">*</span></label>
                    <select id="property_id" name="property_id" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('property_id') border-red-500 @enderror">
                        <option value="">Choose a property...</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }} - {{ $property->address }}
                            </option>
                        @endforeach
                    </select>
                    
                    @error('property_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Request Title -->
                <div class="mb-6">
                    <label for="title" class="block text-gray-700 text-sm font-medium mb-2">Request Title <span class="text-red-500">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                        placeholder="e.g., Leaky faucet in kitchen">
                    
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-gray-700 text-sm font-medium mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="4" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                        placeholder="Please describe the issue in detail...">{{ old('description') }}</textarea>
                    
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Location -->
                <div class="mb-6">
                    <label for="location" class="block text-gray-700 text-sm font-medium mb-2">Location <span class="text-red-500">*</span></label>
                    <input id="location" type="text" name="location" value="{{ old('location') }}" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location') border-red-500 @enderror"
                        placeholder="e.g., Kitchen, Unit 2B, Basement">
                    
                    @error('location')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Priority -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-4">Priority <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative priority-option">
                            <input type="radio" id="priority-low" name="priority" value="low" class="absolute opacity-0" {{ old('priority') == 'low' ? 'checked' : '' }}>
                            <label for="priority-low" class="block cursor-pointer">
                                <div class="font-medium text-blue-600 mb-1">LOW</div>
                                <div class="text-sm text-gray-500">Non-urgent, can be addressed later.</div>
                            </label>
                        </div>
                        
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative priority-option">
                            <input type="radio" id="priority-medium" name="priority" value="medium" class="absolute opacity-0" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}>
                            <label for="priority-medium" class="block cursor-pointer">
                                <div class="font-medium text-yellow-600 mb-1">MEDIUM</div>
                                <div class="text-sm text-gray-500">Should be addressed soon.</div>
                            </label>
                        </div>
                        
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative priority-option">
                            <input type="radio" id="priority-high" name="priority" value="high" class="absolute opacity-0" {{ old('priority') == 'high' ? 'checked' : '' }}>
                            <label for="priority-high" class="block cursor-pointer">
                                <div class="font-medium text-red-600 mb-1">HIGH</div>
                                <div class="text-sm text-gray-500">Urgent, needs immediate attention.</div>
                            </label>
                        </div>
                    </div>
                    
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Images -->
                <div class="mb-6">
                    <label for="images" class="block text-gray-700 text-sm font-medium mb-2">Images (Optional)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload images</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF up to 2MB
                            </p>
                        </div>
                    </div>
                    
                    @error('images.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Submit Request
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Priority selection styling
document.querySelectorAll('.priority-option').forEach(option => {
    const radio = option.querySelector('input[type="radio"]');
    const label = option.querySelector('label');
    
    radio.addEventListener('change', function() {
        // Remove active class from all options
        document.querySelectorAll('.priority-option').forEach(opt => {
            opt.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        // Add active class to selected option
        if (this.checked) {
            option.classList.add('border-blue-500', 'bg-blue-50');
        }
    });
    
    // Set initial state
    if (radio.checked) {
        option.classList.add('border-blue-500', 'bg-blue-50');
    }
});

// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const container = this.closest('.border-dashed');
    
    // Clear existing previews
    const existingPreviews = container.querySelectorAll('.image-preview');
    existingPreviews.forEach(preview => preview.remove());
    
    // Create previews for new files
    files.forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'image-preview mt-2';
                preview.innerHTML = `
                    <img src="${e.target.result}" class="w-20 h-20 object-cover rounded border" alt="Preview">
                `;
                container.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush 