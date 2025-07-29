@extends('mobile.layout')
@section('title', 'Add Property')
@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <h2 class="text-center text-lg font-bold mb-4">Add Property</h2>
        <form method="POST" action="{{ route('mobile.properties.store') }}" enctype="multipart/form-data" id="property-form">
            @csrf
            <div class="mb-3">
                <label class="block font-semibold mb-1">Name*</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Address*</label>
                <input type="text" name="address" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Property Owner*</label>
                <select name="owner_id" class="w-full border rounded p-2" required>
                    <option value="">Select an owner</option>
                    @if(isset($owners))
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->displayName }}</option>
                        @endforeach
                    @endif
                </select>
                @if(!isset($owners) || $owners->count() === 0)
                    <div class="text-yellow-600 text-xs mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        No owners found. <a href="/m/ao/create" class="underline hover:text-yellow-800">Create an owner first</a>.
                    </div>
                @endif
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Special Instructions</label>
                <textarea name="special_instructions" class="w-full border rounded p-2"></textarea>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Property Image</label>
                <input type="file" name="image" id="property-image-input" class="w-full border rounded p-2" accept="image/*">
                <div class="text-xs text-gray-500 mt-1">Image will be automatically optimized for upload</div>
                <div id="file-info" class="text-xs text-blue-600 mt-1 hidden"></div>
                <div id="error-info" class="text-xs text-red-600 mt-1 hidden"></div>
                <div id="image-preview" class="mt-2 hidden">
                    <img id="preview-img" class="w-full h-32 object-cover rounded border" alt="Preview">
                </div>
            </div>
            <button type="submit" id="submit-btn" class="w-full bg-blue-700 text-white py-2 rounded">Add Property</button>
        </form>
    </div>
</div>

<script>
// Ultra-aggressive image resize and validation
const input = document.getElementById('property-image-input');
const previewContainer = document.getElementById('image-preview');
const previewImg = document.getElementById('preview-img');
const fileInfo = document.getElementById('file-info');
const errorInfo = document.getElementById('error-info');
const submitBtn = document.getElementById('submit-btn');
const form = document.getElementById('property-form');

let imageProcessed = false;
let finalFileSize = 0;

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showError(message) {
    errorInfo.textContent = message;
    errorInfo.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Fix Image Issue';
    submitBtn.classList.remove('bg-blue-700', 'bg-green-600');
    submitBtn.classList.add('bg-red-600');
}

function clearError() {
    errorInfo.classList.add('hidden');
    submitBtn.disabled = false;
}

input.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) {
        previewContainer.classList.add('hidden');
        fileInfo.classList.add('hidden');
        clearError();
        imageProcessed = false;
        return;
    }
    
    clearError();
    
    // Check file type
    if (!file.type.startsWith('image/')) {
        showError('Please select an image file');
        return;
    }
    
    // Show original file size
    fileInfo.textContent = `Processing... Original: ${formatFileSize(file.size)}`;
    fileInfo.classList.remove('hidden');
    
    const reader = new FileReader();
    reader.onload = function(event) {
        const img = new Image();
        img.onload = function() {
            // Ultra-aggressive sizing - max 500px and target under 300KB
            const MAX_SIZE = 500;
            const TARGET_SIZE_KB = 300;
            const ABSOLUTE_MAX_KB = 800; // Hard limit before we give up
            
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
            
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(img, 0, 0, width, height);
            
            // Ultra-aggressive compression
            let quality = 0.5; // Start very low
            let attempts = 0;
            const maxAttempts = 8;
            
            function tryCompress() {
                canvas.toBlob(function(blob) {
                    const sizeKB = blob.size / 1024;
                    
                    console.log(`Compression attempt ${attempts + 1}: ${formatFileSize(blob.size)} (quality: ${quality})`);
                    
                    // If file is still too large and we haven't tried enough times, compress more
                    if (sizeKB > TARGET_SIZE_KB && attempts < maxAttempts && quality > 0.1) {
                        attempts++;
                        quality -= 0.05;
                        if (quality < 0.1) quality = 0.1;
                        tryCompress();
                        return;
                    }
                    
                    // Check if file is still too large even after maximum compression
                    if (sizeKB > ABSOLUTE_MAX_KB) {
                        showError(`Image too large even after compression (${formatFileSize(blob.size)}). Please use a smaller image.`);
                        return;
                    }
                    
                    // Create final file
                    const resizedFile = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    
                    finalFileSize = resizedFile.size;
                    imageProcessed = true;
                    
                    // Update input file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(resizedFile);
                    input.files = dataTransfer.files;
                    
                    // Show preview and final size
                    previewImg.src = canvas.toDataURL('image/jpeg', quality);
                    previewContainer.classList.remove('hidden');
                    
                    fileInfo.textContent = `✓ Optimized: ${formatFileSize(file.size)} → ${formatFileSize(resizedFile.size)}`;
                    
                    // Update button based on final size
                    if (resizedFile.size < 512 * 1024) { // Under 512KB
                        submitBtn.textContent = 'Add Property ✓';
                        submitBtn.classList.remove('bg-blue-700', 'bg-orange-600');
                        submitBtn.classList.add('bg-green-600');
                    } else if (resizedFile.size < 1024 * 1024) { // Under 1MB
                        submitBtn.textContent = 'Add Property (Optimized)';
                        submitBtn.classList.remove('bg-blue-700', 'bg-green-600');
                        submitBtn.classList.add('bg-orange-600');
                    } else {
                        showError(`File still too large: ${formatFileSize(resizedFile.size)}`);
                        return;
                    }
                    
                }, 'image/jpeg', quality);
            }
            
            tryCompress();
        };
        
        img.onerror = function() {
            showError('Failed to load image. Please try a different file.');
        };
        
        img.src = event.target.result;
    };
    
    reader.onerror = function() {
        showError('Failed to read file. Please try again.');
    };
    
    reader.readAsDataURL(file);
});

// Prevent form submission if image is too large or not processed
form.addEventListener('submit', function(e) {
    // If there's an image input but no file or not processed
    if (input.files.length > 0 && !imageProcessed) {
        e.preventDefault();
        showError('Please wait for image processing to complete');
        return;
    }
    
    // Double-check file size before submission
    if (input.files.length > 0 && finalFileSize > 1024 * 1024) { // 1MB limit
        e.preventDefault();
        showError('Image is still too large. Please select a smaller image.');
        return;
    }
    
    // Form is good to submit
    submitBtn.textContent = 'Adding Property...';
    submitBtn.disabled = true;
});

// Reset button text on input change
['name', 'address'].forEach(fieldName => {
    const field = document.querySelector(`input[name="${fieldName}"]`);
    if (field) {
        field.addEventListener('input', function() {
            if (submitBtn.textContent.includes('✓') || submitBtn.textContent.includes('Optimized')) {
                // Keep the optimized status but reset to normal blue
                submitBtn.textContent = 'Add Property';
                submitBtn.classList.remove('bg-green-600', 'bg-orange-600');
                submitBtn.classList.add('bg-blue-700');
            }
        });
    }
});
</script>
@endsection 