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
            
            <!-- Checklist Selection (Primary Option) -->
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center mb-3">
                    <input type="checkbox" id="use-checklist" name="use_checklist" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="toggleChecklistSelection()">
                    <label for="use-checklist" class="font-semibold text-blue-800">Use Checklist</label>
                </div>
                <div id="checklist-dropdown" class="hidden">
                    <select name="checklist_id" id="checklist-select" class="w-full border rounded p-2" onchange="updateFormFields()">
                        <option value="">Select a checklist</option>
                        @foreach(auth()->user()->checklists as $checklist)
                            <option value="{{ $checklist->id }}" data-name="{{ $checklist->name }}" data-description="{{ $checklist->generateFormattedDescription() }}">{{ $checklist->name }} ({{ $checklist->items->count() }} items)</option>
                        @endforeach
                    </select>
                    <div class="text-xs text-blue-600 mt-1">Select a checklist to auto-fill the request details</div>
                </div>
            </div>
            
            <!-- Manual Form Fields -->
            <div id="manual-fields">
                <div class="mb-3">
                    <label class="block font-semibold mb-1">Title*</label>
                    <input type="text" name="title" id="title-field" class="w-full border rounded p-2" placeholder="e.g., Leaky faucet in kitchen" required>
                </div>
                <div class="mb-3">
                    <label class="block font-semibold mb-1">Description*</label>
                    <textarea name="description" id="description-field" class="w-full border rounded p-2" placeholder="Please describe the issue in detail..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="block font-semibold mb-1">Location*</label>
                    <input type="text" name="location" id="location-field" class="w-full border rounded p-2" placeholder="e.g., Kitchen, Unit 2B, Basement" required>
                </div>
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
                <div class="text-xs text-gray-500 mt-1">Images will be automatically optimized for upload</div>
                <div id="file-info" class="text-xs text-blue-600 mt-1 hidden"></div>
                <div id="image-previews" class="mt-2 grid grid-cols-2 gap-2 hidden"></div>
            </div>
            <button type="submit" id="submit-btn" class="w-full bg-blue-700 text-white py-2 rounded">Submit Request</button>
        </form>
    </div>
</div>

<script>
// Toggle checklist dropdown visibility
function toggleChecklistSelection() {
    const useChecklist = document.getElementById('use-checklist');
    const checklistDropdown = document.getElementById('checklist-dropdown');
    const manualFields = document.getElementById('manual-fields');
    const titleField = document.getElementById('title-field');
    const descriptionField = document.getElementById('description-field');
    const locationField = document.getElementById('location-field');
    
    if (useChecklist.checked) {
        // Show checklist dropdown
        checklistDropdown.classList.remove('hidden');
        
        // Disable manual fields
        titleField.disabled = true;
        descriptionField.disabled = true;
        locationField.disabled = true;
        
        // Remove required attributes
        titleField.removeAttribute('required');
        descriptionField.removeAttribute('required');
        locationField.removeAttribute('required');
        
        // Visual feedback
        manualFields.style.opacity = '0.6';
        manualFields.style.pointerEvents = 'none';
    } else {
        // Hide checklist dropdown
        checklistDropdown.classList.add('hidden');
        
        // Clear checklist selection
        document.getElementById('checklist-select').value = '';
        
        // Enable manual fields
        titleField.disabled = false;
        descriptionField.disabled = false;
        locationField.disabled = false;
        
        // Add required attributes back
        titleField.setAttribute('required', 'required');
        descriptionField.setAttribute('required', 'required');
        locationField.setAttribute('required', 'required');
        
        // Clear and enable manual fields
        titleField.value = '';
        descriptionField.value = '';
        locationField.value = '';
        
        // Visual feedback
        manualFields.style.opacity = '1';
        manualFields.style.pointerEvents = 'auto';
    }
}

// Update form fields when checklist is selected
function updateFormFields() {
    const checklistSelect = document.getElementById('checklist-select');
    const titleField = document.getElementById('title-field');
    const descriptionField = document.getElementById('description-field');
    const locationField = document.getElementById('location-field');
    
    if (checklistSelect.value) {
        // Checklist selected - auto-fill fields
        const selectedOption = checklistSelect.options[checklistSelect.selectedIndex];
        const checklistName = selectedOption.getAttribute('data-name');
        const checklistDescription = selectedOption.getAttribute('data-description');
        
        titleField.value = checklistName;
        descriptionField.value = checklistDescription;
        locationField.value = '-';
    } else {
        // No checklist selected - clear fields
        titleField.value = '';
        descriptionField.value = '';
        locationField.value = '';
    }
}

// Enhanced image resize and preview functionality for multiple images
const input = document.getElementById('request-images-input');
const form = document.getElementById('request-form');
const previewContainer = document.getElementById('image-previews');
const fileInfo = document.getElementById('file-info');
const submitBtn = document.getElementById('submit-btn');

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

input.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    if (!files.length) {
        previewContainer.classList.add('hidden');
        fileInfo.classList.add('hidden');
        return;
    }
    
    previewContainer.innerHTML = '';
    previewContainer.classList.remove('hidden');
    
    let originalSize = 0;
    let optimizedSize = 0;
    files.forEach(file => originalSize += file.size);
    
    fileInfo.textContent = `Processing ${files.length} image(s)... Original: ${formatFileSize(originalSize)}`;
    fileInfo.classList.remove('hidden');
    
    const resizedFiles = [];
    let processedCount = 0;
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                // Aggressive sizing for maintenance request images
                const MAX_SIZE = 600;
                const TARGET_SIZE_KB = 400; // Even smaller for multiple images
                
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
                
                // Aggressive compression
                let quality = 0.6; // Start lower for multiple images
                let attempts = 0;
                const maxAttempts = 5;
                
                function tryCompress() {
                    canvas.toBlob(function(blob) {
                        const sizeKB = blob.size / 1024;
                        
                        // If file is still too large, compress more
                        if (sizeKB > TARGET_SIZE_KB && attempts < maxAttempts) {
                            attempts++;
                            quality -= 0.1;
                            if (quality < 0.1) quality = 0.1;
                            tryCompress();
                            return;
                        }
                        
                        const resizedFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        
                        resizedFiles[index] = resizedFile;
                        optimizedSize += resizedFile.size;
                        processedCount++;
                        
                        // Show preview
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'relative';
                        const previewImg = document.createElement('img');
                        previewImg.src = canvas.toDataURL('image/jpeg', quality);
                        previewImg.className = 'w-full h-20 object-cover rounded border';
                        
                        const sizeLabel = document.createElement('div');
                        sizeLabel.className = 'text-xs text-center mt-1 text-gray-600';
                        sizeLabel.textContent = formatFileSize(resizedFile.size);
                        
                        previewDiv.appendChild(previewImg);
                        previewDiv.appendChild(sizeLabel);
                        previewContainer.appendChild(previewDiv);
                        
                        // Update input files when all images are processed
                        if (processedCount === files.length) {
                            const dataTransfer = new DataTransfer();
                            resizedFiles.forEach(file => {
                                if (file) dataTransfer.items.add(file);
                            });
                            input.files = dataTransfer.files;
                            
                            // Update file info
                            fileInfo.textContent = `${files.length} image(s): ${formatFileSize(originalSize)} → ${formatFileSize(optimizedSize)}`;
                            
                            // Update button
                            const totalSizeMB = optimizedSize / (1024 * 1024);
                            if (totalSizeMB < 2) { // Under 2MB total
                                submitBtn.textContent = 'Submit Request ✓';
                                submitBtn.classList.remove('bg-blue-700');
                                submitBtn.classList.add('bg-green-600');
                            } else {
                                submitBtn.textContent = 'Submit Request (Large Files)';
                                submitBtn.classList.remove('bg-blue-700');
                                submitBtn.classList.add('bg-orange-600');
                            }
                        }
                    }, 'image/jpeg', quality);
                }
                
                tryCompress();
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
});

// Reset button when form is submitted
form.addEventListener('submit', function() {
    submitBtn.textContent = 'Submitting Request...';
    submitBtn.disabled = true;
});
</script>
@endsection 