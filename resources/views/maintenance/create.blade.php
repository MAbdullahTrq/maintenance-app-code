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
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-1">Property*</label>
                    <select id="property_id" name="property_id" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Select a property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Checklist Selection (Primary Option) -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <label for="checklist_id" class="block text-sm font-medium text-blue-800 mb-2">Use Checklist</label>
                    <select id="checklist_id" name="checklist_id" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="toggleFormFields()">
                        <option value="">No checklist - Fill form manually</option>
                        @foreach($checklists as $checklist)
                            <option value="{{ $checklist->id }}" data-name="{{ $checklist->name }}" data-description="{{ $checklist->generateFormattedDescription() }}" {{ old('checklist_id') == $checklist->id ? 'selected' : '' }}>
                                {{ $checklist->name }} ({{ $checklist->items->count() }} items)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-blue-600 text-xs mt-1">Select a checklist to auto-fill the request details</p>
                    @error('checklist_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Manual Form Fields -->
                <div id="manual-fields">
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title*</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g., Broken Sink" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description*</label>
                        <textarea id="description" name="description" rows="4" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Describe the issue in detail..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location*</label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g., Kitchen, Bathroom, Living Room" required>
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority*</label>
                    <select id="priority" name="priority" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
            @if(Auth::user()->isPropertyManager())
            <!-- Email Updates Section (Managers Only) -->
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg" data-email-updates>
                    <label class="block text-sm font-medium text-green-800 mb-3">Email Updates</label>
                    <p class="text-green-600 text-sm mb-3">Select team members who should receive email updates about this request:</p>
                    <div class="space-y-2">
                        @if(isset($editorTeamMembers) && $editorTeamMembers->count() > 0)
                            @foreach($editorTeamMembers as $member)
                                <label class="flex items-center">
                                    <input type="checkbox" name="email_updates[]" value="{{ $member->id }}" 
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                        {{ in_array($member->id, old('email_updates', [])) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">{{ $member->name }} ({{ $member->email }})</span>
                                </label>
                            @endforeach
                        @else
                            <p class="text-gray-500 text-sm">No editor team members available for email updates.</p>
                        @endif
                    </div>
                    @error('email_updates')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                
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
// Form field toggling based on checklist selection
function toggleFormFields() {
    const checklistSelect = document.getElementById('checklist_id');
    const manualFields = document.getElementById('manual-fields');
    const titleField = document.getElementById('title');
    const descriptionField = document.getElementById('description');
    const locationField = document.getElementById('location');
    
    if (checklistSelect.value) {
        // Checklist selected - auto-fill and disable manual fields
        const selectedOption = checklistSelect.options[checklistSelect.selectedIndex];
        const checklistName = selectedOption.getAttribute('data-name');
        const checklistDescription = selectedOption.getAttribute('data-description');
        
        titleField.value = checklistName;
        descriptionField.value = checklistDescription;
        // Location field remains enabled and required
        
        // Disable manual fields (except location)
        titleField.disabled = true;
        descriptionField.disabled = true;
        locationField.disabled = false; // Explicitly keep location field enabled
        
        // Remove required attributes (except location)
        titleField.removeAttribute('required');
        descriptionField.removeAttribute('required');
        locationField.setAttribute('required', 'required'); // Explicitly keep location required
        
        // Visual feedback - only apply to title and description fields, not location
        titleField.style.opacity = '0.6';
        descriptionField.style.opacity = '0.6';
        titleField.style.pointerEvents = 'none';
        descriptionField.style.pointerEvents = 'none';
        
        // Keep location field fully functional
        locationField.style.opacity = '1';
        locationField.style.pointerEvents = 'auto';
    } else {
        // No checklist - enable manual fields
        titleField.value = '';
        descriptionField.value = '';
        locationField.value = '';
        
        // Enable manual fields
        titleField.disabled = false;
        descriptionField.disabled = false;
        locationField.disabled = false;
        
        // Add required attributes back
        titleField.setAttribute('required', 'required');
        descriptionField.setAttribute('required', 'required');
        locationField.setAttribute('required', 'required');
        
        // Visual feedback - reset all fields
        titleField.style.opacity = '1';
        descriptionField.style.opacity = '1';
        locationField.style.opacity = '1';
        titleField.style.pointerEvents = 'auto';
        descriptionField.style.pointerEvents = 'auto';
        locationField.style.pointerEvents = 'auto';
    }
}

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

// Filter team members based on property selection
document.addEventListener('DOMContentLoaded', function() {
    const propertySelect = document.getElementById('property_id');
    const emailUpdatesSection = document.querySelector('[data-email-updates]');
    
    if (propertySelect && emailUpdatesSection) {
        propertySelect.addEventListener('change', function() {
            const propertyId = this.value;
            
            if (propertyId) {
                // Fetch assigned team members for this property
                fetch(`/api/properties/${propertyId}/assigned-team-members`)
                    .then(response => response.json())
                    .then(data => {
                        // Update the email updates checkboxes
                        const checkboxes = emailUpdatesSection.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(checkbox => {
                            const userId = checkbox.value;
                            const isAssigned = data.assigned_team_members.includes(parseInt(userId));
                            
                            if (isAssigned) {
                                checkbox.closest('label').style.display = 'flex';
                                checkbox.disabled = false;
                            } else {
                                checkbox.closest('label').style.display = 'none';
                                checkbox.checked = false;
                                checkbox.disabled = true;
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching assigned team members:', error);
                    });
            } else {
                // Reset all checkboxes when no property is selected
                const checkboxes = emailUpdatesSection.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.closest('label').style.display = 'flex';
                    checkbox.disabled = false;
                    checkbox.checked = false;
                });
            }
        });
    }
});
</script>
@endsection 