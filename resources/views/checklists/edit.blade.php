@extends('layouts.app')

@section('title', 'Edit Checklist')
@section('header', 'Edit Checklist')

@push('styles')
<style>
.sortable-ghost {
    opacity: 0.4;
}
.sortable-chosen {
    transform: rotate(5deg);
}
.sortable-drag {
    opacity: 0.8;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Checklist Details Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-6">Checklist Details</h2>

            <form action="{{ route('checklists.update', $checklist) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Checklist Name *</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $checklist->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $checklist->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>

        <!-- New Inline Checklist Items Editor -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold">Checklist Items</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="itemCount">{{ $checklist->items->count() }} items</span>
                </div>
            </div>

            <!-- Inline Add Item Interface -->
            <div class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <!-- Field Name Input -->
                    <div class="col-span-5">
                        <input type="text" 
                               id="newItemFieldName" 
                               placeholder="Field name*" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    </div>
                    
                    <!-- Type Dropdown -->
                    <div class="col-span-3">
                        <select id="newItemType" 
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="checkbox">Checkbox</option>
                            <option value="text">Text</option>
                            <option value="header">Header</option>
                        </select>
                    </div>
                    
                    <!-- Required Checkbox -->
                    <div class="col-span-2">
                        <label class="flex items-center justify-center">
                            <input type="checkbox" 
                                   id="newItemRequired" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Required</span>
                        </label>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-span-2 flex justify-end space-x-2">
                        <button type="button" 
                                id="previewBtn"
                                class="px-3 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                            Preview
                        </button>
                        <button type="button" 
                                id="saveChecklistBtn"
                                class="px-3 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            Save Checklist
                        </button>
                    </div>
                </div>
                
                <!-- Task Description Input (appears when checkbox/text is selected) -->
                <div id="taskDescriptionRow" class="mt-3 hidden">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-10">
                            <input type="text" 
                                   id="newItemDescription" 
                                   placeholder="Task description" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        </div>
                        <div class="col-span-2 flex justify-end">
                            <button type="button" 
                                    id="addAttachmentBtn"
                                    class="px-3 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                                <i class="fas fa-paperclip"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checklist Items Container -->
            <div id="checklistItemsContainer" class="space-y-3">
                <!-- Existing items will be loaded here -->
            </div>
        </div>

        <!-- Checklist Items Display (New Design) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Checklist Items</h3>

            @if($checklist->items->count() > 0)
                <div class="space-y-3" id="existingItemsContainer">
                    @foreach($checklist->items as $item)
                        <div class="checklist-item border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-shadow" 
                             data-existing-item-id="{{ $item->id }}" 
                             data-item-type="{{ $item->type }}"
                             draggable="true">
                            <div class="flex items-center">
                                <!-- Drag Handle -->
                                <div class="drag-handle flex-shrink-0 mr-3 cursor-move text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                
                                <!-- Item Content -->
                                <div class="flex-1 flex items-center">
                                    <!-- Left Section (Empty for most items, checkbox for checkbox items) -->
                                    <div class="w-8 h-8 flex-shrink-0 mr-3">
                                        @if($item->type === 'checkbox')
                                            <div class="w-6 h-6 border-2 border-gray-300 rounded flex items-center justify-center">
                                                <i class="fas fa-check text-green-600 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Right Section (Content) -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900">
                                                    {{ $item->description }}
                                                    @if($item->is_required)
                                                        <span class="text-red-500 ml-1">*</span>
                                                    @endif
                                                </h4>
                                                @if($item->attachment_path)
                                                    <div class="mt-1">
                                                        <a href="{{ $item->attachment_url }}" 
                                                           target="_blank"
                                                           class="text-sm text-blue-600 hover:text-blue-800">
                                                            <i class="fas fa-paperclip mr-1"></i>View Attachment
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="flex items-center space-x-2 ml-4">
                                                <button onclick="editExistingItem({{ $item->id }})" 
                                                        class="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="duplicateItem({{ $item->id }})" 
                                                        class="p-1 text-gray-400 hover:text-green-600 transition-colors"
                                                        title="Duplicate">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <form method="POST" 
                                                      action="{{ route('checklists.items.destroy', [$checklist, $item]) }}" 
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
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-2">
                        <i class="fas fa-list-ul text-4xl"></i>
                    </div>
                    <p class="text-gray-500">No checklist items yet</p>
                    <p class="text-sm text-gray-400">Add items using the form above</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Checklist Item</h3>
            <form id="editItemForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="edit_type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                    <select name="type" 
                            id="edit_type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="text">Text</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <input type="text" 
                           name="description" 
                           id="edit_description" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>

                <div class="mb-4">
                    <label for="edit_attachment" class="block text-sm font-medium text-gray-700 mb-2">Attachment</label>
                    <input type="file" 
                           name="attachment" 
                           id="edit_attachment" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           accept="image/*,.pdf,.doc,.docx">
                </div>

                <div class="mb-4" id="editRequiredField" style="display: none;">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_required" 
                               value="1"
                               id="edit_is_required"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Required (for checkbox items)</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeEditModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup new inline editing functionality
    setupInlineEditing();
    setupDragAndDrop();
    loadExistingItems();
});

function setupInlineEditing() {
    const fieldNameInput = document.getElementById('newItemFieldName');
    const typeSelect = document.getElementById('newItemType');
    const requiredCheckbox = document.getElementById('newItemRequired');
    const descriptionInput = document.getElementById('newItemDescription');
    const taskDescriptionRow = document.getElementById('taskDescriptionRow');
    const saveChecklistBtn = document.getElementById('saveChecklistBtn');
    
    // Show/hide task description based on type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'checkbox' || this.value === 'text') {
            taskDescriptionRow.classList.remove('hidden');
        } else {
            taskDescriptionRow.classList.add('hidden');
        }
    });
    
    // Auto-save when clicking out of field name input
    fieldNameInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            addItemFromInlineForm();
        }
    });
    
    // Auto-save when pressing Enter in field name
    fieldNameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.value.trim()) {
                addItemFromInlineForm();
            }
        }
    });
    
    // Auto-save when clicking out of description input
    descriptionInput.addEventListener('blur', function() {
        if (fieldNameInput.value.trim() && this.value.trim()) {
            addItemFromInlineForm();
        }
    });
    
    // Auto-save when pressing Enter in description
    descriptionInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (fieldNameInput.value.trim() && this.value.trim()) {
                addItemFromInlineForm();
            }
        }
    });
    
    // Save checklist button
    saveChecklistBtn.addEventListener('click', function() {
        if (fieldNameInput.value.trim()) {
            addItemFromInlineForm();
        }
    });
}

function addItemFromInlineForm() {
    const fieldName = document.getElementById('newItemFieldName').value.trim();
    const type = document.getElementById('newItemType').value;
    const required = document.getElementById('newItemRequired').checked;
    const description = document.getElementById('newItemDescription').value.trim();
    
    if (!fieldName) return;
    
    // Create item data
    const itemData = {
        description: fieldName,
        type: type,
        is_required: required,
        attachment_path: null
    };
    
    // Add description if provided and type supports it
    if (description && (type === 'checkbox' || type === 'text')) {
        itemData.description = description;
    }
    
    // Add item to checklist
    addItemToChecklist(itemData);
    
    // Clear form
    clearInlineForm();
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
    itemDiv.setAttribute('draggable', 'true');
    
    const leftSection = itemData.type === 'checkbox' ? 
        '<div class="w-6 h-6 border-2 border-gray-300 rounded flex items-center justify-center"><i class="fas fa-check text-green-600 text-sm"></i></div>' :
        '<div class="w-6 h-6"></div>';
    
    itemDiv.innerHTML = `
        <div class="flex items-center">
            <div class="drag-handle flex-shrink-0 mr-3 cursor-move text-gray-400 hover:text-gray-600">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <div class="flex-1 flex items-center">
                <div class="w-8 h-8 flex-shrink-0 mr-3">
                    ${leftSection}
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">
                                ${itemData.description}
                                ${itemData.is_required ? '<span class="text-red-500 ml-1">*</span>' : ''}
                            </h4>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <button onclick="editExistingItem(this)" 
                                    class="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="duplicateItem(this)" 
                                    class="p-1 text-gray-400 hover:text-green-600 transition-colors"
                                    title="Duplicate">
                                <i class="fas fa-copy"></i>
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
    document.getElementById('taskDescriptionRow').classList.add('hidden');
}

function updateItemCount() {
    const container = document.getElementById('existingItemsContainer');
    const count = container.children.length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function saveItemToServer(itemData) {
    // This would make an AJAX call to save the item
    // For now, we'll just log it
    console.log('Saving item:', itemData);
    
    // TODO: Implement AJAX call to save item
    // fetch('/checklists/{{ $checklist->id }}/items', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json',
    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //     },
    //     body: JSON.stringify(itemData)
    // });
}

function handleEnterKey(event, input) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const row = input.closest('.checklist-item-row');
        const saveBtn = row.querySelector('.save-item-btn');
        saveItem(saveBtn);
    }
}

function saveItem(button) {
    const row = button.closest('.checklist-item-row');
    const typeSelect = row.querySelector('.item-type');
    const descriptionInput = row.querySelector('.item-description');
    const requiredCheckbox = row.querySelector('.item-required');
    
    if (!descriptionInput.value.trim()) {
        alert('Please enter a description for the checklist item.');
        descriptionInput.focus();
        return;
    }
    
    const formData = new FormData();
    formData.append('type', typeSelect.value);
    formData.append('description', descriptionInput.value.trim());
    formData.append('is_required', requiredCheckbox.checked ? '1' : '0');
    formData.append('_token', '{{ csrf_token() }}');
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch('{{ route("checklists.items.store", $checklist) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the row with the new item ID
            row.setAttribute('data-item-id', data.item.id);
            row.classList.remove('bg-gray-50');
            row.classList.add('bg-white');
            
            // Change save button to update button
            button.innerHTML = '<i class="fas fa-edit"></i>';
            button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            button.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
            button.onclick = () => updateItem(button, data.item.id);
            button.disabled = false;
            
            // Add to existing items display
            addToExistingItemsDisplay(data.item);
            
            // Clear the input for next item
            descriptionInput.value = '';
            requiredCheckbox.checked = false;
            
            // Add new row for next item
            addNewItemRow();
            
            showNotification('Item added successfully!', 'success');
        } else {
            throw new Error(data.message || 'Failed to add item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-save"></i>';
        showNotification('Error adding item: ' + error.message, 'error');
    });
}

function updateItem(button, itemId) {
    const row = button.closest('.checklist-item-row');
    const typeSelect = row.querySelector('.item-type');
    const descriptionInput = row.querySelector('.item-description');
    const requiredCheckbox = row.querySelector('.item-required');
    
    if (!descriptionInput.value.trim()) {
        alert('Please enter a description for the checklist item.');
        descriptionInput.focus();
        return;
    }
    
    const formData = new FormData();
    formData.append('type', typeSelect.value);
    formData.append('description', descriptionInput.value.trim());
    formData.append('is_required', requiredCheckbox.checked ? '1' : '0');
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch(`{{ route('checklists.items.update', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 
        if (data.success) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
            
            // Update existing items display
            updateExistingItemsDisplay(itemId, {
                type: typeSelect.value,
                description: descriptionInput.value.trim(),
                is_required: requiredCheckbox.checked
            });
        } else {
            throw new Error(data.message || 'Failed to update item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-edit"></i>';
        showNotification('Error updating item: ' + error.message, 'error');
    });
}

function removeItemRow(button) {
    const row = button.closest('.checklist-item-row');
    const itemId = row.getAttribute('data-item-id');
    
    if (itemId && itemId !== 'new') {
        // Delete from server
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    removeFromExistingItemsDisplay(itemId);
                    updateItemCount();
                    showNotification('Item deleted successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting item: ' + error.message, 'error');
            });
        }
    } else {
        // Just remove the row (new item not saved yet)
        row.remove();
        updateItemCount();
    }
}

function addToExistingItemsDisplay(item) {
    const container = document.getElementById('existingItemsContainer');
    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="${item.id}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${item.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
                        </span>
                        ${item.is_required ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>' : ''}
                    </div>
                    <p class="text-sm font-medium text-gray-900">${item.description}</p>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editExistingItem(${item.id})" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteExistingItem(${item.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateExistingItemsDisplay(itemId, data) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const typeSpan = itemElement.querySelector('.inline-flex');
        const descriptionP = itemElement.querySelector('.text-sm.font-medium');
        const requiredSpan = itemElement.querySelector('.bg-red-100');
        
        typeSpan.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`;
        typeSpan.textContent = data.type.charAt(0).toUpperCase() + data.type.slice(1);
        descriptionP.textContent = data.description;
        
        if (data.is_required) {
            if (!requiredSpan) {
                typeSpan.insertAdjacentHTML('afterend', '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Required</span>');
            }
        } else {
            if (requiredSpan) {
                requiredSpan.remove();
            }
        }
    }
}

function removeFromExistingItemsDisplay(itemId) {
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        itemElement.remove();
    }
}

function updateItemCount() {
    const container = document.getElementById('checklistItemsContainer');
    const count = container.querySelectorAll('.checklist-item-row').length;
    document.getElementById('itemCount').textContent = `${count} items`;
}

function setupSearch() {
    const searchInput = document.getElementById('searchItems');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.checklist-item-row');
        
        items.forEach(item => {
            const description = item.querySelector('.item-description').value.toLowerCase();
            const type = item.querySelector('.item-type').value.toLowerCase();
            
            if (description.includes(searchTerm) || type.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
    const order = Array.from(items).map(item => item.getAttribute('data-existing-item-id'));
    
    // Send AJAX request to update order
    fetch(`/checklists/{{ $checklist->id }}/items/order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
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

function copyItem(itemId) {
    // Find the item in the existing items
    const itemElement = document.querySelector(`[data-existing-item-id="${itemId}"]`);
    if (itemElement) {
        const description = itemElement.querySelector('h4').textContent;
        const type = itemElement.getAttribute('data-item-type');
        const required = itemElement.getAttribute('data-item-required') === 'true';
        
        // Fill the form with the copied item data
        document.getElementById('newItemFieldName').value = description;
        document.getElementById('newItemType').value = type;
        document.getElementById('newItemDescription').value = description;
        document.getElementById('newItemRequired').checked = required;
        
        // Focus on the field name input
        document.getElementById('newItemFieldName').focus();
        
        showNotification('Item copied to form', 'info');
    }
}

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                removeFromExistingItemsDisplay(itemId);
                updateItemCount();
                showNotification('Item deleted successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to delete item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting item: ' + error.message, 'error');
        });
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 