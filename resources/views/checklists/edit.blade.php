@extends('layouts.app')

@section('title', 'Edit Checklist')
@section('header', 'Edit Checklist')

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
                        Update Checklist
                    </button>
                </div>
            </form>
        </div>

        <!-- Dynamic Checklist Items Editor -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Checklist Items</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="itemCount">{{ $checklist->items->count() }} items</span>
                    <button type="button" 
                            onclick="addNewItemRow()"
                            class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors text-sm">
                        <i class="fas fa-plus mr-1"></i>Add Item
                    </button>
                </div>
            </div>

            <!-- Search/Filter Bar -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" 
                           id="searchItems" 
                           placeholder="Search checklist items..." 
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Dynamic Items Container -->
            <div id="checklistItemsContainer" class="space-y-3">
                <!-- Existing items will be loaded here -->
            </div>

            <!-- Add Item Row Template (hidden) -->
            <div id="newItemTemplate" class="hidden">
                <div class="checklist-item-row border border-gray-200 rounded-lg p-4 bg-gray-50" data-item-id="new">
                    <div class="flex items-center space-x-3">
                        <!-- Type Dropdown -->
                        <div class="flex-shrink-0 w-24">
                            <select class="item-type w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="checkbox">Check</option>
                                <option value="text">Text</option>
                            </select>
                        </div>
                        
                        <!-- Description Field -->
                        <div class="flex-1">
                            <input type="text" 
                                   class="item-description w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                   placeholder="Enter checklist item description..."
                                   onkeypress="handleEnterKey(event, this)">
                        </div>
                        
                        <!-- Required Checkbox -->
                        <div class="flex-shrink-0">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       class="item-required rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-xs text-gray-600">Required</span>
                            </label>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex-shrink-0 flex items-center space-x-1">
                            <button type="button" 
                                    onclick="saveItem(this)" 
                                    class="save-item-btn px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">
                                <i class="fas fa-save"></i>
                            </button>
                            <button type="button" 
                                    onclick="removeItemRow(this)" 
                                    class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Existing Items Display -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Current Checklist Items</h3>

            @if($checklist->items->count() > 0)
                <div class="space-y-4" id="existingItemsContainer">
                    @foreach($checklist->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4" data-existing-item-id="{{ $item->id }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->type === 'checkbox' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                        @if($item->is_required)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Required
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->description }}</p>
                                    @if($item->attachment_path)
                                        <div class="mt-2">
                                            <a href="{{ $item->attachment_url }}" 
                                               target="_blank"
                                               class="text-sm text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-paperclip mr-1"></i>View Attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <button onclick="editExistingItem({{ $item->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" 
                                          action="{{ route('checklists.items.destroy', [$checklist, $item]) }}" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No items added yet. Use the dynamic editor above to add checklist items.</p>
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
    // Load existing items into dynamic editor
    loadExistingItems();
    
    // Setup search functionality
    setupSearch();
});

function loadExistingItems() {
    const container = document.getElementById('checklistItemsContainer');
    const existingItems = @json($checklist->items);
    
    existingItems.forEach(item => {
        addItemRow(item);
    });
}

function addItemRow(existingItem = null) {
    const container = document.getElementById('checklistItemsContainer');
    const template = document.getElementById('newItemTemplate');
    const newRow = template.querySelector('.checklist-item-row').cloneNode(true);
    
    if (existingItem) {
        // Load existing item data
        newRow.setAttribute('data-item-id', existingItem.id);
        newRow.classList.remove('bg-gray-50');
        newRow.classList.add('bg-white');
        
        const typeSelect = newRow.querySelector('.item-type');
        const descriptionInput = newRow.querySelector('.item-description');
        const requiredCheckbox = newRow.querySelector('.item-required');
        const saveBtn = newRow.querySelector('.save-item-btn');
        
        typeSelect.value = existingItem.type;
        descriptionInput.value = existingItem.description;
        requiredCheckbox.checked = existingItem.is_required;
        
        // Change save button to update button
        saveBtn.innerHTML = '<i class="fas fa-edit"></i>';
        saveBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        saveBtn.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
        saveBtn.onclick = () => updateItem(saveBtn, existingItem.id);
    } else {
        // New item - focus on description field
        const descriptionInput = newRow.querySelector('.item-description');
        setTimeout(() => descriptionInput.focus(), 100);
    }
    
    container.appendChild(newRow);
    updateItemCount();
}

function addNewItemRow() {
    addItemRow();
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
    
    fetch(`{{ route('checklists.items.store', $checklist) }}/${itemId}`, {
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
            
            fetch(`{{ route('checklists.items.store', $checklist) }}/${itemId}`, {
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

function editExistingItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function deleteExistingItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');
        
        fetch(`{{ route('checklists.items.store', $checklist) }}/${itemId}`, {
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