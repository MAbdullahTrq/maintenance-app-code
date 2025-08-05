@extends('layouts.guest')

@section('title', 'Submit Maintenance Request')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Submit a Maintenance Request</h2>
                <p class="text-gray-600 mt-2">Property: <span class="font-semibold">{{ $property->name }}</span></p>
            </div>
            
            <form method="POST" action="{{ route('guest.request.submit', $property->access_link) }}" enctype="multipart/form-data" id="guest-request-form">
                @csrf
                
                <div class="mb-6">
                    <label for="title" class="block text-gray-700 text-sm font-medium mb-2">Request Title <span class="text-red-500">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                        placeholder="e.g., Leaky faucet in kitchen">
                    
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="description" class="block text-gray-700 text-sm font-medium mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="4" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                        placeholder="Please describe the issue in detail...">{{ old('description') }}</textarea>
                    
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="location" class="block text-gray-700 text-sm font-medium mb-2">Location <span class="text-red-500">*</span></label>
                    <input id="location" type="text" name="location" value="{{ old('location') }}" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location') border-red-500 @enderror"
                        placeholder="e.g., Kitchen, Unit 2B, Basement">
                    
                    @error('location')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-4">Priority <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative priority-option">
                            <input type="radio" id="priority-low" name="priority" value="low" class="absolute opacity-0" {{ old('priority') == 'low' ? 'checked' : '' }}>
                            <label for="priority-low" class="block cursor-pointer">
                                <div class="font-medium text-blue-600 mb-1">LOW</div>
                                <div class="text-sm text-gray-500">You can fix after we leave, just wanted to let you know.</div>
                            </label>
                        </div>
                        
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative priority-option">
                            <input type="radio" id="priority-medium" name="priority" value="medium" class="absolute opacity-0" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}>
                            <label for="priority-medium" class="block cursor-pointer">
                                <div class="font-medium text-yellow-600 mb-1">MEDIUM</div>
                                <div class="text-sm text-gray-500">You can fix the next cleaning day is fine.</div>
                            </label>
                        </div>
                        
                        <div class="border rounded-md p-4 cursor-pointer hover:bg-gray-50 relative priority-option">
                            <input type="radio" id="priority-high" name="priority" value="high" class="absolute opacity-0" {{ old('priority') == 'high' ? 'checked' : '' }}>
                            <label for="priority-high" class="block cursor-pointer">
                                <div class="font-medium text-red-600 mb-1">HIGH</div>
                                <div class="text-sm text-gray-500">Fix asap please.</div>
                            </label>
                        </div>
                    </div>
                    
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
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
                    
                    <div id="image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3 hidden"></div>
                    
                    @error('images')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    @error('images.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <h3 class="text-gray-700 text-sm font-medium mb-2">Contact Information (Optional)</h3>
                    <p class="text-gray-600 text-sm mb-4">Add your details if you would like to be updated.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Your Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                            
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="phone" class="block text-gray-700 text-sm font-medium mb-2">Phone Number</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                        
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Priority selection styling */
    .priority-option.selected-priority {
        border-width: 2px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    .priority-option.selected-priority.priority-low {
        border-color: #2563eb;
        background-color: #eff6ff;
    }
    
    .priority-option.selected-priority.priority-medium {
        border-color: #d97706;
        background-color: #fffbeb;
    }
    
    .priority-option.selected-priority.priority-high {
        border-color: #dc2626;
        background-color: #fef2f2;
    }
    
    .priority-option {
        transition: all 0.2s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    // Priority selection
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize selected state on page load
        function initializePrioritySelection() {
            document.querySelectorAll('.priority-option').forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                if (radio.checked) {
                    option.classList.add('selected-priority');
                    // Add specific priority class for styling
                    option.classList.add('priority-' + radio.value);
                }
            });
        }
        
        // Handle priority option clicks
        document.querySelectorAll('.priority-option').forEach(option => {
            option.addEventListener('click', function() {
                // Clear selected class from all options
                document.querySelectorAll('.priority-option').forEach(el => {
                    el.classList.remove('selected-priority', 'priority-low', 'priority-medium', 'priority-high');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected-priority');
                
                // Check the radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                // Add specific priority class for styling
                this.classList.add('priority-' + radio.value);
            });
        });
        
        // Initialize on page load
        initializePrioritySelection();
    });

    // Image resize and preview functionality
    const input = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview');

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
                        previewImg.className = 'h-24 w-full object-cover rounded border shadow-sm';
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
@endpush 