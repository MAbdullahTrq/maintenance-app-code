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
                <label class="block font-semibold mb-1">Special Instructions</label>
                <textarea name="special_instructions" class="w-full border rounded p-2"></textarea>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Property Image</label>
                <input type="file" name="image" id="property-image-input" class="w-full border rounded p-2" accept="image/*">
                <div id="image-preview" class="mt-2 hidden">
                    <img id="preview-img" class="w-full h-32 object-cover rounded border" alt="Preview">
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Property</button>
        </form>
    </div>
</div>

<script>
// Image resize and preview functionality
const input = document.getElementById('property-image-input');
const previewContainer = document.getElementById('image-preview');
const previewImg = document.getElementById('preview-img');

input.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) {
        previewContainer.classList.add('hidden');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(event) {
        const img = new Image();
        img.onload = function() {
            const MAX_SIZE = 800;
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
                
                // Update input file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(resizedFile);
                input.files = dataTransfer.files;
                
                // Show preview
                previewImg.src = canvas.toDataURL(file.type, 0.85);
                previewContainer.classList.remove('hidden');
            }, file.type, 0.85);
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endsection 