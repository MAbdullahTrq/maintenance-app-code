@extends('mobile.layout')

@section('title', 'Edit Checklist')

@push('styles')
<style>
.checklist-item {
    transition: all 0.2s ease;
}

.checklist-item:hover {
    transform: translateY(-1px);
}

.checklist-item.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.drag-handle {
    transition: color 0.2s ease;
}

.drag-handle:hover {
    color: #6b7280 !important;
}

.checklist-item[draggable="true"] {
    cursor: move;
}

.checklist-item[draggable="true"]:hover {
    cursor: move;
}

/* Smooth transitions for form elements */
#newItemFieldName, #newItemType, #newItemDescription {
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Enhanced focus states */
#newItemFieldName:focus, #newItemDescription:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Task description row animation */
#taskDescriptionRow {
    transition: all 0.3s ease;
}

/* Editable field styles */
.editable-field {
    position: relative;
}

.editable-display {
    transition: all 0.2s ease;
}

.editable-display:hover {
    background-color: #f9fafb;
    border-color: #d1d5db;
}

.editable-input {
    transition: all 0.2s ease;
}

.editable-field.editing .editable-display {
    display: none !important;
}

.editable-field.editing .editable-input {
    display: block !important;
}

.editable-field.saving .editable-display {
    opacity: 0.6;
}

.editable-field.saving .editable-display::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 10px;
    width: 16px;
    height: 16px;
    border: 2px solid #3b82f6;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translateY(-50%);
}

@keyframes spin {
    0% { transform: translateY(-50%) rotate(0deg); }
    100% { transform: translateY(-50%) rotate(360deg); }
}

/* Force larger text sizes for checklist items */
.checklist-item h4 {
    font-size: 1.125rem !important;
    font-weight: 500 !important;
    line-height: 1.5 !important;
}

.checklist-item h3 {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
    line-height: 1.4 !important;
}

/* Additional specificity for mobile checklist items */
#existingItemsContainer .checklist-item h4 {
    font-size: 1.125rem !important;
    font-weight: 500 !important;
}

/* Force text size for dynamically created items */
.checklist-item[data-item-type] h4 {
    font-size: 1.125rem !important;
    font-weight: 500 !important;
}

#taskDescriptionRow.hidden {
    display: none !important;
}

#taskDescriptionRow:not(.hidden) {
    display: block !important;
}

/* Button hover effects */
button {
    transition: all 0.2s ease;
}

button:hover {
    transform: translateY(-1px);
}

/* Drag handle styling */
.drag-handle i {
    font-size: 14px;
}

/* Item type indicators */
.checklist-item[data-item-type="checkbox"] .w-6.h-6 {
    border-color: #10b981;
}

.checklist-item[data-item-type="text"] .w-6.h-6 {
    border-color: #6b7280;
}

.checklist-item[data-item-type="header"] .w-6.h-6 {
    border-color: #3b82f6;
}

/* Mobile-specific adjustments */
@media (max-width: 768px) {
    .grid-cols-12 > div {
        margin-bottom: 0.5rem;
    }
    
    .col-span-5, .col-span-3, .col-span-2 {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .checklist-item {
        padding: 0.75rem;
    }
    
    .drag-handle {
        margin-right: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-4xl mx-auto">
        <div class="mb-4">
            <a href="/m/cl" class="text-blue-500 hover:text-blue-700 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back to Checklists
            </a>
        </div>

        <!-- Checklist Details - Inline Editable -->
        <div class="mb-6">
            <!-- Editable Title -->
            <div class="editable-field mb-1" data-field="name" data-original-value="{{ $checklist->name }}">
                <div class="editable-display cursor-pointer hover:bg-gray-50 transition-all duration-200 p-2 -m-2 rounded">
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight">
                        {{ $checklist->name ?: 'Click to add title' }}
                    </h1>
                    <i class="fas fa-edit text-gray-400 text-sm mt-1 opacity-0 hover:opacity-100 transition-opacity"></i>
                </div>
                    <input type="text" 
                       class="editable-input w-full text-2xl font-bold text-gray-900 border-none outline-none bg-transparent p-2 -m-2 rounded focus:bg-gray-50"
                       value="{{ $checklist->name }}"
                       placeholder="Enter checklist title"
                       style="display: none;">
                </div>

            <!-- Editable Description -->
            <div class="editable-field" data-field="description" data-original-value="{{ $checklist->description }}">
                <div class="editable-display cursor-pointer hover:bg-gray-50 transition-all duration-200 p-2 -m-2 rounded">
                    <p class="text-base text-gray-600 leading-relaxed">
                        {{ $checklist->description ?: 'Click to add description' }}
                    </p>
                    <i class="fas fa-edit text-gray-400 text-sm mt-1 opacity-0 hover:opacity-100 transition-opacity"></i>
                </div>
                <textarea class="editable-input w-full text-base text-gray-600 border-none outline-none bg-transparent p-2 -m-2 rounded focus:bg-gray-50 resize-none"
                              rows="3"
                          placeholder="Enter checklist description"
                          style="display: none;">{{ $checklist->description }}</textarea>
            </div>
                </div>


        <!-- New Inline Checklist Items Editor -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold" style="font-size: 1.25rem !important;">Checklist Items</h3>
                    <span class="text-sm text-gray-500" id="itemCount">{{ $checklist->items->count() }} items</span>
                </div>

            <!-- Inline Add Item Interface -->
            <div class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                <!-- First Row: Field Name & Type -->
                <div class="grid grid-cols-2 gap-4 items-center mb-3">
                    <!-- Field Name Input -->
                    <div>
                        <input type="text" 
                               id="newItemFieldName" 
                               placeholder="Field name*" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
        </div>

                    <!-- Type Dropdown -->
                    <div>
                        <select id="newItemType" 
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="checkbox">Checkbox</option>
                            <option value="header">Header</option>
                        </select>
            </div>
        </div>

                <!-- Second Row: Task Description & Required/Attachment -->
                <div id="taskDescriptionRow" class="grid grid-cols-2 gap-4 items-center mb-3" style="display: block !important;">
                    <div>
                        <input type="text" 
                               id="newItemDescription" 
                               placeholder="Task description" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   id="newItemRequired" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Required</span>
                        </label>
                <div class="flex items-center space-x-2">
                            <!-- Attachment thumbnails container -->
                            <div id="attachmentThumbnails" class="hidden flex space-x-1">
                                <!-- Thumbnails will be dynamically added here -->
                            </div>
                    <button type="button" 
                                    id="addAttachmentBtn"
                                    class="px-3 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                                <i class="fas fa-paperclip"></i>
                    </button>
                    </div>

                        <!-- Hidden file input for attachments -->
                        <input type="file" 
                               id="attachmentInput" 
                               accept="image/*" 
                               multiple
                               style="display: none;">
                    </div>
                    </div>

                <!-- Action Buttons -->
                <div class="flex justify-end">
                    <button type="button" 
                            id="saveChecklistBtn"
                            class="px-3 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                        Add
                    </button>
                </div>
                </div>

            <!-- Checklist Items Container -->
            <div id="checklistItemsContainer" class="space-y-3">
                <!-- Existing items will be loaded here -->
            </div>
            </div>

        <!-- Current Checklist Items Display -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-xl font-semibold mb-4" style="font-size: 1.25rem !important;">Current Checklist Items</h3>

            <div class="space-y-3" id="existingItemsContainer">
                @if($checklist->items->count() > 0)
                    @foreach($checklist->items as $item)
                        <div class="checklist-item border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-shadow" 
                             data-existing-item-id="{{ $item->id }}" 
                             data-item-type="{{ $item->type }}"
                             data-item-required="{{ $item->is_required ? 'true' : 'false' }}"
                             data-item-attachment="{{ $item->attachment_path ? $item->attachment_url : '' }}"
                             data-item-task-description="{{ $item->task_description }}"
                             draggable="true">
                            <div class="flex items-center">
                                <!-- Drag Handle -->
                                <div class="drag-handle flex-shrink-0 mr-3 cursor-move text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                        </div>
                        
                                <!-- Item Content -->
                                <div class="flex-1 flex items-center">
                                    <!-- Left Section (Empty for most items, checkbox for checkbox items) -->
                                    <div class="w-10 h-10 flex-shrink-0 mr-3">
                                        @if($item->type === 'checkbox')
                                            <div class="w-8 h-8 border-2 border-gray-300 rounded flex items-center justify-center">
                                                <i class="fas fa-check text-green-600 text-base"></i>
                                            </div>
                                        @endif
                </div>

                                    <!-- Right Section (Content) -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                @if($item->type === 'header')
                                                    <h3 class="font-bold text-gray-900 text-2xl" style="font-size: 1.5rem !important;">
                                                        {{ $item->description }}
                                                    </h3>
                                                @else
                                                    <h4 class="font-normal text-gray-900 text-lg" style="font-size: 1.125rem !important;">
                                                        {{ $item->description }}
                                                        @if($item->is_required)
                                                            <span class="text-red-500 ml-1">*</span>
                                                        @endif
                                                    </h4>
                                                @endif
                                                @if($item->task_description)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $item->task_description }}</p>
                                                @endif
                                                @if($item->hasAttachments())
                                                    <div class="mt-2">
                                                        <div class="flex space-x-1">
                                                            @foreach($item->getAllAttachmentPaths() as $attachmentPath)
                                                                @php
                                                                    $isImage = in_array(strtolower(pathinfo($attachmentPath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                    $attachmentUrl = asset('storage/' . $attachmentPath);
                                                                @endphp
                                                                
                                                                @if($isImage)
                                                                    <img src="{{ $attachmentUrl }}" 
                                                                         alt="Attachment" 
                                                                         class="w-8 h-8 object-cover rounded border border-gray-200 cursor-pointer hover:scale-110 transition-transform duration-200"
                                                                         onclick="openImageModal('{{ $attachmentUrl }}')">
                                                                @else
                                                                    <a href="{{ $attachmentUrl }}" 
                                                                       target="_blank"
                                                                       class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                                                        <i class="fas fa-paperclip mr-1"></i>View
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                </div>

                        <!-- Action Buttons -->
                                            <div class="flex items-center space-x-2 ml-4">
                                                <button onclick="editExistingItem(this)" 
                                                        class="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                            </button>
                                                <form method="POST" 
                                                      action="{{ route('mobile.checklists.items.destroy', [$checklist, $item]) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this item?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-1 text-gray-400 hover:text-red-600 transition-colors"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                    </button>
                                                </form>
                </div>
                                </div>
                </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                
                <!-- Empty state message (hidden by default, shown via JavaScript when needed) -->
                <div id="emptyStateMessage" class="text-center py-8 hidden">
                    <div class="text-gray-400 mb-2">
                        <i class="fas fa-list-ul text-4xl"></i>
                    </div>
                    <p class="text-gray-500">No checklist items yet</p>
                    <p class="text-sm text-gray-400">Add items using the form above</p>
                            </div>
                        </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup new inline editing functionality
    setupInlineEditing();
    setupDragAndDrop();
    loadExistingItems();
    
    // Initialize description field visibility based on default type selection
    initializeDescriptionField();
    
    // Setup inline editing for checklist details
    setupChecklistDetailsEditing();
});

function initializeDescriptionField() {
    const typeSelect = document.getElementById('newItemType');
    const taskDescriptionRow = document.getElementById('taskDescriptionRow');
    
    // Show/hide based on current type selection
    if (typeSelect.value === 'checkbox') {
        taskDescriptionRow.classList.remove('hidden');
        taskDescriptionRow.style.display = 'block';
    } else {
        taskDescriptionRow.classList.add('hidden');
        taskDescriptionRow.style.display = 'none';
    }
}

function setupInlineEditing() {
    const fieldNameInput = document.getElementById('newItemFieldName');
    const typeSelect = document.getElementById('newItemType');
    const requiredCheckbox = document.getElementById('newItemRequired');
    const descriptionInput = document.getElementById('newItemDescription');
    const taskDescriptionRow = document.getElementById('taskDescriptionRow');
    const saveChecklistBtn = document.getElementById('saveChecklistBtn');
    const attachmentBtn = document.getElementById('addAttachmentBtn');
    const attachmentInput = document.getElementById('attachmentInput');
    
    // Show/hide task description based on type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'checkbox') {
            taskDescriptionRow.classList.remove('hidden');
            taskDescriptionRow.style.display = 'block';
        } else {
            taskDescriptionRow.classList.add('hidden');
            taskDescriptionRow.style.display = 'none';
        }
    });
    
    // Handle attachment button click
    attachmentBtn.addEventListener('click', function() {
        attachmentInput.click();
    });
    
    // Handle file selection
    attachmentInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            // Check if we already have files and adding more would exceed limit
            if (compressedImageFiles.length + files.length > 4) {
                showNotification('Maximum 4 images allowed', 'error');
                return;
            }
            
            // Process each file
            files.forEach(file => {
                if (compressedImageFiles.length < 4) {
                    compressAndPreviewImage(file);
                }
            });
        }
    });
    
    // Handle Enter key in field name input
    fieldNameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Just focus on description field if it's visible, otherwise focus on save button
            if (taskDescriptionRow.classList.contains('hidden')) {
                saveChecklistBtn.focus();
            } else {
                descriptionInput.focus();
            }
        }
    });
    
    // Handle Enter key in description input
    descriptionInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveChecklistBtn.focus();
        }
    });
    
    // Add focus-outside detection for the entire form
    const formContainer = document.querySelector('.border-2.border-dashed.border-gray-300');
    if (formContainer) {
        // Track if form has been modified
        let formModified = false;
        
        // Mark form as modified when inputs change
        [fieldNameInput, typeSelect, requiredCheckbox, descriptionInput].forEach(input => {
            input.addEventListener('input', function() {
                formModified = true;
            });
            input.addEventListener('change', function() {
                formModified = true;
            });
        });
        
        // Handle focus outside the form
        document.addEventListener('click', function(e) {
            if (formModified && !formContainer.contains(e.target)) {
                // Check if we have valid data to save
                const fieldName = fieldNameInput.value.trim();
                const type = typeSelect.value;
                
                if (fieldName && (type === 'header' || type === 'checkbox')) {
                    addItemFromInlineForm();
                }
            }
        });
        
        // Reset form modified flag when form is cleared
        const originalClearForm = clearInlineForm;
        clearInlineForm = function() {
            formModified = false;
            originalClearForm();
        };
    }
    
    // Save checklist button
    saveChecklistBtn.addEventListener('click', function() {
        const currentType = typeSelect.value;
        if (currentType === 'header' && fieldNameInput.value.trim()) {
            addItemFromInlineForm();
        } else if (currentType === 'checkbox' && fieldNameInput.value.trim()) {
            addItemFromInlineForm();
        } else if (currentType === 'text' && fieldNameInput.value.trim() && descriptionInput.value.trim()) {
            addItemFromInlineForm();
        } else {
            // Provide specific error messages
            if (currentType === 'header') {
                showNotification('Please enter a header name', 'error');
            } else if (currentType === 'checkbox') {
                showNotification('Please enter a field name', 'error');
            } else if (currentType === 'text') {
                if (!fieldNameInput.value.trim()) {
                    showNotification('Please enter a field name', 'error');
                } else if (!descriptionInput.value.trim()) {
                    showNotification('Please enter a task description', 'error');
                }
            } else {
                showNotification('Please fill in all required fields', 'error');
            }
        }
    });
}

function addItemFromInlineForm() {
    const fieldName = document.getElementById('newItemFieldName').value.trim();
    const type = document.getElementById('newItemType').value;
    const required = document.getElementById('newItemRequired').checked;
    const description = document.getElementById('newItemDescription').value.trim();
    const saveBtn = document.getElementById('saveChecklistBtn');
    const isEdit = saveBtn.hasAttribute('data-edit-item-id');
    
    // For header type, only field name is required
    if (type === 'header') {
        if (!fieldName) {
            showNotification('Please enter a header name', 'error');
            return;
        }
        
        const itemData = {
            description: fieldName,
            task_description: description,
            type: type,
            is_required: required,
            attachment_path: null
        };
        
        if (isEdit) {
            updateItemOnServer(itemData, saveBtn.getAttribute('data-edit-item-id'));
    } else {
            addItemToChecklist(itemData);
        }
        clearInlineForm();
        return;
    }
    
    // For checkbox type, only field name is required
    if (type === 'checkbox') {
        if (!fieldName) {
            showNotification('Please enter a field name', 'error');
            return;
        }
        
        const itemData = {
            description: fieldName,
            task_description: description,
            type: type,
            is_required: required,
            attachment_path: null
        };
        
        if (isEdit) {
            updateItemOnServer(itemData, saveBtn.getAttribute('data-edit-item-id'));
        } else {
            addItemToChecklist(itemData);
        }
        clearInlineForm();
        return;
    }
    
}

function addItemToChecklist(itemData) {
    // Create new item element
    const itemElement = createChecklistItemElement(itemData);
    
    // Add to container
    const container = document.getElementById('existingItemsContainer');
    container.appendChild(itemElement);
    
    // Update item count
    updateItemCount();
    
    // Save to server
    saveItemToServer(itemData);
}

function createChecklistItemElement(itemData) {
    const itemDiv = document.createElement('div');
    itemDiv.className = 'checklist-item border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-shadow';
    itemDiv.setAttribute('data-item-type', itemData.type);
    itemDiv.setAttribute('data-item-required', itemData.is_required ? 'true' : 'false');
    itemDiv.setAttribute('draggable', 'true');
    
    const leftSection = itemData.type === 'checkbox' ? 
        '<div class="w-8 h-8 border-2 border-gray-300 rounded flex items-center justify-center"><i class="fas fa-check text-green-600 text-base"></i></div>' :
        '<div class="w-8 h-8"></div>';
    
    itemDiv.innerHTML = `
        <div class="flex items-center">
            <div class="drag-handle flex-shrink-0 mr-3 cursor-move text-gray-400 hover:text-gray-600">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <div class="flex-1 flex items-center">
                <div class="w-10 h-10 flex-shrink-0 mr-3">
                    ${leftSection}
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="font-normal text-gray-900 text-lg" style="font-size: 1.125rem !important;">
                                ${itemData.description}
                                ${itemData.is_required ? '<span class="text-red-500 ml-1">*</span>' : ''}
                            </h4>
                            ${itemData.task_description ? `<p class="text-sm text-gray-600 mt-1">${itemData.task_description}</p>` : ''}
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <button onclick="editExistingItem(this)" 
                                    class="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteItem(this)" 
                                    class="p-1 text-gray-400 hover:text-red-600 transition-colors"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    return itemDiv;
}

function clearInlineForm() {
    document.getElementById('newItemFieldName').value = '';
    document.getElementById('newItemType').value = 'checkbox';
    document.getElementById('newItemRequired').checked = false;
    document.getElementById('newItemDescription').value = '';
    
    // Show description field since checkbox is default
    const taskDescriptionRow = document.getElementById('taskDescriptionRow');
    taskDescriptionRow.classList.remove('hidden');
    taskDescriptionRow.style.display = 'block';
    
    // Reset attachments
    compressedImageFiles = [];
    document.getElementById('attachmentInput').value = '';
    const attachmentBtn = document.getElementById('addAttachmentBtn');
    attachmentBtn.innerHTML = '<i class="fas fa-paperclip"></i>';
    attachmentBtn.title = '';
    
    // Hide attachment thumbnails
    const attachmentThumbnails = document.getElementById('attachmentThumbnails');
    attachmentThumbnails.classList.add('hidden');
    
    // Reset edit state
    const saveBtn = document.getElementById('saveChecklistBtn');
    saveBtn.removeAttribute('data-edit-item-id');
    saveBtn.textContent = 'Add';
}

function updateItemCount() {
    const container = document.getElementById('existingItemsContainer');
    const items = container.querySelectorAll('.checklist-item');
    const count = items.length;
    document.getElementById('itemCount').textContent = `${count} items`;
    
    // Show/hide empty state message
    const emptyStateMessage = document.getElementById('emptyStateMessage');
    if (count === 0) {
        emptyStateMessage.classList.remove('hidden');
    } else {
        emptyStateMessage.classList.add('hidden');
    }
}

function saveItemToServer(itemData) {
    const formData = new FormData();
    formData.append('type', itemData.type);
    formData.append('description', itemData.description);
    formData.append('task_description', itemData.task_description || '');
    formData.append('is_required', itemData.is_required ? '1' : '0');
    formData.append('_token', '{{ csrf_token() }}');
    
    // Add attachments if available
    compressedImageFiles.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file);
    });
    
    fetch('{{ route("mobile.checklists.items.store", $checklist) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the item element with the server ID
            const container = document.getElementById('existingItemsContainer');
            const lastItem = container.lastElementChild;
            if (lastItem && data.item) {
                lastItem.setAttribute('data-existing-item-id', data.item.id);
            }
            showNotification('Item added successfully!', 'success');
        } else {
            showNotification('Error adding item: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding item. Please try again.', 'error');
    });
}

function updateItemOnServer(itemData, itemId) {
    const formData = new FormData();
    formData.append('type', itemData.type);
    formData.append('description', itemData.description);
    formData.append('task_description', itemData.task_description || '');
    formData.append('is_required', itemData.is_required ? '1' : '0');
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    // Add attachments if available
    compressedImageFiles.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file);
    });
    
    fetch(`{{ route("mobile.checklists.items.update", [$checklist, '']) }}/${itemId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated item
            location.reload();
        } else {
            showNotification('Error updating item: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating item. Please try again.', 'error');
    });
}

function setupDragAndDrop() {
    const container = document.getElementById('existingItemsContainer');
    
    if (container && typeof Sortable !== 'undefined') {
        // Make the container sortable
        new Sortable(container, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                // Update the order of items
                updateItemOrder();
            }
        });
    }
}

function updateItemOrder() {
    const container = document.getElementById('existingItemsContainer');
    const items = container.querySelectorAll('.checklist-item');
    const order = Array.from(items).map((item, index) => ({
        id: item.getAttribute('data-existing-item-id'),
        order: index + 1
    }));
    
    // Send AJAX request to update order
    fetch(`/m/cl/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ items: order })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Item order updated', 'success');
        } else {
            showNotification('Error updating order', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating order', 'error');
    });
}

function loadExistingItems() {
    // Items are already loaded in the HTML, just update count
    updateItemCount();
}

function editExistingItem(button) {
    const itemElement = button.closest('.checklist-item');
    const itemId = itemElement.getAttribute('data-existing-item-id');
    const itemType = itemElement.getAttribute('data-item-type');
    const itemRequired = itemElement.getAttribute('data-item-required') === 'true';
    const itemAttachment = itemElement.getAttribute('data-item-attachment');
    const itemTaskDescription = itemElement.getAttribute('data-item-task-description');
    
    // Get the item description from the h4 element
    const descriptionElement = itemElement.querySelector('h4');
    const description = descriptionElement.textContent.replace(' *', '').trim();
    
    // Fill the form with the existing item data
    document.getElementById('newItemFieldName').value = description;
    document.getElementById('newItemType').value = itemType;
    document.getElementById('newItemRequired').checked = itemRequired;
    
    // Show task description row if needed
    const taskDescriptionRow = document.getElementById('taskDescriptionRow');
    if (itemType === 'checkbox') {
        taskDescriptionRow.classList.remove('hidden');
        taskDescriptionRow.style.display = 'block';
        document.getElementById('newItemDescription').value = itemTaskDescription || '';
    } else {
        taskDescriptionRow.classList.add('hidden');
        taskDescriptionRow.style.display = 'none';
    }
    
    // Handle existing attachment
    const attachmentThumbnail = document.getElementById('attachmentThumbnail');
    const attachmentThumbnailImg = document.getElementById('attachmentThumbnailImg');
    const attachmentBtn = document.getElementById('addAttachmentBtn');
    
    if (itemAttachment) {
        // Show existing attachment thumbnail
        attachmentThumbnailImg.src = itemAttachment;
        attachmentThumbnail.classList.remove('hidden');
        attachmentBtn.innerHTML = '<i class="fas fa-image text-green-600"></i>';
        attachmentBtn.title = 'Replace attachment';
    } else {
        // Hide thumbnail and reset button
        attachmentThumbnail.classList.add('hidden');
        attachmentBtn.innerHTML = '<i class="fas fa-paperclip"></i>';
        attachmentBtn.title = '';
    }
    
    // Clear any new attachment
    compressedImageFile = null;
    document.getElementById('attachmentInput').value = '';
    
    // Store the item ID for updating
    document.getElementById('saveChecklistBtn').setAttribute('data-edit-item-id', itemId);
    document.getElementById('saveChecklistBtn').textContent = 'Update Item';
    
    // Focus on the field name input
    document.getElementById('newItemFieldName').focus();
    
    showNotification('Item loaded for editing', 'info');
}


function deleteItem(button) {
    if (confirm('Are you sure you want to delete this item?')) {
        const itemElement = button.closest('.checklist-item');
        const itemId = itemElement.getAttribute('data-existing-item-id');
        
        // Disable the button to prevent multiple clicks
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        if (itemId) {
            // Delete from server
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`/m/cl/{{ $checklist->id }}/items/${itemId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    itemElement.remove();
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    showNotification('Error deleting item: ' + (data.message || 'Unknown error'), 'error');
                    // Re-enable button on error
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Check if it's a 404 error (item not found)
                if (error.message.includes('404') || error.message.includes('No query results')) {
                    // Item was already deleted, just remove from DOM
                    itemElement.remove();
                    updateItemCount();
                    showNotification('Item was already deleted', 'info');
                } else {
                    showNotification('Error deleting item. Please try again.', 'error');
                    // Re-enable button on error
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash"></i>';
                }
            });
        } else {
            // Just remove from DOM if no ID (shouldn't happen with existing items)
            itemElement.remove();
            updateItemCount();
        }
    }
}


function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Global variable to store compressed images
let compressedImageFiles = [];

function compressAndPreviewImage(file) {
    if (!file.type.startsWith('image/')) {
        showNotification('Please select an image file', 'error');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            // Compress image similar to maintenance requests
            const MAX_SIZE = 800;
            const TARGET_SIZE_KB = 500;
            
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
            
            // Compress with quality adjustment
            let quality = 0.7;
            let attempts = 0;
            const maxAttempts = 5;
            
            function tryCompress() {
                canvas.toBlob(function(blob) {
                    const sizeKB = blob.size / 1024;
                    
                    if (sizeKB <= TARGET_SIZE_KB || attempts >= maxAttempts) {
                        // Create new file from compressed blob
                        const compressedFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        
                        // Add to compressed files array
                        compressedImageFiles.push(compressedFile);
                        
                        // Update attachment button
                        const attachmentBtn = document.getElementById('addAttachmentBtn');
                        attachmentBtn.innerHTML = `<i class="fas fa-image text-green-600"></i> (${compressedImageFiles.length}/4)`;
                        attachmentBtn.title = `${compressedImageFiles.length} image(s) attached`;
                        
                        // Show thumbnails
                        updateAttachmentThumbnails();
                        
                        showNotification(`Image ${compressedImageFiles.length} compressed and ready to attach`, 'success');
                    } else {
                        quality -= 0.1;
                        attempts++;
                        tryCompress();
                    }
                }, 'image/jpeg', quality);
            }
            
            tryCompress();
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function updateAttachmentThumbnails() {
    const thumbnailsContainer = document.getElementById('attachmentThumbnails');
    thumbnailsContainer.innerHTML = '';
    
    compressedImageFiles.forEach((file, index) => {
        const thumbnail = document.createElement('div');
        thumbnail.className = 'relative';
        
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'w-8 h-8 object-cover rounded border border-gray-200 cursor-pointer hover:scale-110 transition-transform duration-200';
        img.onclick = () => openImageModal(img.src);
        
        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '×';
        removeBtn.className = 'absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full hover:bg-red-600';
        removeBtn.onclick = (e) => {
            e.stopPropagation();
            removeAttachment(index);
        };
        
        thumbnail.appendChild(img);
        thumbnail.appendChild(removeBtn);
        thumbnailsContainer.appendChild(thumbnail);
    });
    
    if (compressedImageFiles.length > 0) {
        thumbnailsContainer.classList.remove('hidden');
    } else {
        thumbnailsContainer.classList.add('hidden');
    }
}

function removeAttachment(index) {
    compressedImageFiles.splice(index, 1);
    updateAttachmentThumbnails();
    
    const attachmentBtn = document.getElementById('addAttachmentBtn');
    if (compressedImageFiles.length === 0) {
        attachmentBtn.innerHTML = '<i class="fas fa-paperclip"></i>';
        attachmentBtn.title = '';
    } else {
        attachmentBtn.innerHTML = `<i class="fas fa-image text-green-600"></i> (${compressedImageFiles.length}/4)`;
        attachmentBtn.title = `${compressedImageFiles.length} image(s) attached`;
    }
}

function openImageModal(imageUrl) {
    // Create modal overlay
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    };
    
    // Create modal content
    const modalContent = document.createElement('div');
    modalContent.className = 'max-w-4xl max-h-full p-4';
    
    const img = document.createElement('img');
    img.src = imageUrl;
    img.className = 'max-w-full max-h-full object-contain rounded';
    img.onclick = function(e) {
        e.stopPropagation();
    };
    
    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.className = 'absolute top-4 right-4 text-white text-2xl hover:text-gray-300';
    closeBtn.onclick = function() {
        modal.remove();
    };
    
    modalContent.appendChild(img);
    modal.appendChild(modalContent);
    modal.appendChild(closeBtn);
    
    document.body.appendChild(modal);
}

function setupChecklistDetailsEditing() {
    // Use event delegation for better reliability
    document.addEventListener('click', function(e) {
        const display = e.target.closest('.editable-display');
        if (display) {
            e.preventDefault();
            e.stopPropagation();
            
            const field = display.closest('.editable-field');
            const input = field.querySelector('.editable-input');
            const fieldName = field.getAttribute('data-field');
            const originalValue = field.getAttribute('data-original-value');
            
            startEditing(field, display, input);
        }
    });
    
    document.addEventListener('blur', function(e) {
        if (e.target.classList.contains('editable-input')) {
            const field = e.target.closest('.editable-field');
            const display = field.querySelector('.editable-display');
            const fieldName = field.getAttribute('data-field');
            const originalValue = field.getAttribute('data-original-value');
            
            saveField(field, display, e.target, fieldName, originalValue);
        }
    }, true);
    
    document.addEventListener('keypress', function(e) {
        if (e.target.classList.contains('editable-input') && e.target.tagName === 'INPUT' && e.key === 'Enter') {
            e.target.blur();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.target.classList.contains('editable-input') && e.key === 'Escape') {
            const field = e.target.closest('.editable-field');
            const display = field.querySelector('.editable-display');
            const originalValue = field.getAttribute('data-original-value');
            
            cancelEditing(field, display, e.target, originalValue);
        }
    });
}

function startEditing(field, display, input) {
    field.classList.add('editing');
    
    // Force show the input and hide the display
    display.style.display = 'none';
    input.style.display = 'block';
    
    input.focus();
    
    // Select all text for easy replacement
    if (input.tagName === 'INPUT') {
        input.select();
    }
}

function saveField(field, display, input, fieldName, originalValue) {
    const newValue = input.value.trim();
    const currentValue = field.getAttribute('data-original-value');
    
    // Don't save if value hasn't changed
    if (newValue === currentValue) {
        cancelEditing(field, display, input, currentValue);
        return;
    }
    
    // Validate required field
    if (fieldName === 'name' && !newValue) {
        showNotification('Checklist name is required', 'error');
        input.focus();
        return;
    }
    
    // Show saving state
    field.classList.remove('editing');
    field.classList.add('saving');
    
    // Prepare form data
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append(fieldName, newValue);
    
    // Send AJAX request
    const checklistId = {{ $checklist->id }};
    fetch(`/m/cl/${checklistId}/edit`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display and original value
            const textElement = display.querySelector('h1, p');
            if (newValue) {
                textElement.textContent = newValue;
            } else {
                textElement.textContent = fieldName === 'name' ? 'Click to add title' : 'Click to add description';
            }
            
            field.setAttribute('data-original-value', newValue);
            
            // Switch back to display mode
            display.style.display = 'block';
            input.style.display = 'none';
            field.classList.remove('editing');
            const fieldLabel = fieldName === 'name' ? 'Title' : 'Description';
            showNotification(`${fieldLabel} updated successfully`, 'success');
        } else {
            showNotification(data.message || 'Failed to update checklist', 'error');
            // Revert to original value
            cancelEditing(field, display, input, currentValue);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update checklist', 'error');
        // Revert to original value
        cancelEditing(field, display, input, currentValue);
    })
    .finally(() => {
        field.classList.remove('saving');
    });
}

function cancelEditing(field, display, input, originalValue) {
    field.classList.remove('editing');
    
    // Force show the display and hide the input
    display.style.display = 'block';
    input.style.display = 'none';
    
    input.value = originalValue;
}
</script>
@endsection 