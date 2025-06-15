@extends('mobile.layout')
@section('title', 'Make a Request')
@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <h2 class="text-center text-lg font-bold mb-4">Make a Request</h2>
        <form method="POST" action="{{ route('mobile.requests.store') }}" enctype="multipart/form-data" id="request-form">
            @csrf
            <div class="mb-3">
                <label class="block font-semibold mb-1">Property*</label>
                <select name="property_id" class="w-full border rounded p-2" required>
                    <option value="">Select Property</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Title*</label>
                <input type="text" name="title" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Description*</label>
                <textarea name="description" class="w-full border rounded p-2" required></textarea>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Location*</label>
                <input type="text" name="location" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Priority*</label>
                <select name="priority" class="w-full border rounded p-2" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Images</label>
                <input type="file" name="images[]" id="request-images-input" class="w-full border rounded p-2" multiple accept="image/*">
                <div id="image-previews" class="mt-2 grid grid-cols-2 gap-2 hidden"></div>
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Submit Request</button>
        </form>
    </div>
</div>

<script>
// Image resize and preview functionality
const input = document.getElementById('request-images-input');
const form = document.getElementById('request-form');
const previewContainer = document.getElementById('image-previews');

input.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    if (!files.length) return;
    
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
                    previewImg.className = 'w-full h-20 object-cover rounded border';
                    previewDiv.appendChild(previewImg);
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