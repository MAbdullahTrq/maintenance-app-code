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
            
            <form method="POST" action="{{ route('guest.request.submit', $property->access_link) }}" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="title" class="block text-gray-700 text-sm font-medium mb-2">Request Title <span class="text-red-500">*</span></label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                        placeholder="Brief description of the issue">
                    
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="location" class="block text-gray-700 text-sm font-medium mb-2">Location <span class="text-red-500">*</span></label>
                    <input id="location" type="text" name="location" value="{{ old('location') }}" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location') border-red-500 @enderror"
                        placeholder="Room number, area, or specific location">
                    
                    @error('location')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Priority <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <div class="relative border rounded-md p-4 hover:bg-gray-50 cursor-pointer priority-option {{ old('priority') == 'low' ? 'selected-priority' : '' }}">
                            <input type="radio" id="priority-low" name="priority" value="low" class="absolute opacity-0 priority-radio" {{ old('priority') == 'low' ? 'checked' : '' }}>
                            <label for="priority-low" class="block cursor-pointer">
                                <div class="font-medium text-blue-600 mb-1">LOW</div>
                                <div class="text-sm text-gray-500">You can fix after we leave, just wanted to let you know.</div>
                            </label>
                        </div>
                        
                        <div class="relative border rounded-md p-4 hover:bg-gray-50 cursor-pointer priority-option {{ old('priority', 'medium') == 'medium' ? 'selected-priority' : '' }}">
                            <input type="radio" id="priority-medium" name="priority" value="medium" class="absolute opacity-0 priority-radio" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}>
                            <label for="priority-medium" class="block cursor-pointer">
                                <div class="font-medium text-yellow-600 mb-1">MEDIUM</div>
                                <div class="text-sm text-gray-500">You can fix the next cleaning day is fine.</div>
                            </label>
                        </div>
                        
                        <div class="relative border rounded-md p-4 hover:bg-gray-50 cursor-pointer priority-option {{ old('priority') == 'high' ? 'selected-priority' : '' }}">
                            <input type="radio" id="priority-high" name="priority" value="high" class="absolute opacity-0 priority-radio" {{ old('priority') == 'high' ? 'checked' : '' }}>
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
                    <label for="description" class="block text-gray-700 text-sm font-medium mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="4" required
                        class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                        placeholder="Please provide detailed information about the issue">{{ old('description') }}</textarea>
                    
                    @error('description')
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
                    
                    @error('images')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    @error('images.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <h3 class="text-gray-700 text-sm font-medium mb-4">Contact Information (Optional)</h3>
                    
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
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                        
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .selected-priority {
        background-color: #f0f9ff;
        border-color: #3b82f6;
        border-width: 2px;
    }
</style>

<script>
    // Priority selection
    document.querySelectorAll('.priority-option').forEach(option => {
        option.addEventListener('click', function() {
            // Clear selected class from all options
            document.querySelectorAll('.priority-option').forEach(el => {
                el.classList.remove('selected-priority');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected-priority');
            
            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
        });
    });

    // Preview images before upload
    document.getElementById('images').addEventListener('change', function(event) {
        const preview = document.createElement('div');
        preview.className = 'grid grid-cols-2 md:grid-cols-3 gap-2 mt-2';
        preview.id = 'image-preview';
        
        const oldPreview = document.getElementById('image-preview');
        if (oldPreview) {
            oldPreview.remove();
        }
        
        const files = event.target.files;
        
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-24 w-full object-cover rounded';
                    
                    div.appendChild(img);
                    preview.appendChild(div);
                }
                
                reader.readAsDataURL(file);
            }
            
            event.target.parentElement.parentElement.parentElement.parentElement.appendChild(preview);
        }
    });
</script>
@endpush 