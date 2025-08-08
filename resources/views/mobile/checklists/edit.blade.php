@extends('mobile.layout')

@section('title', 'Edit Checklist')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-4xl mx-auto">
        <div class="mb-4">
            <a href="/m/cl" class="text-blue-500 hover:text-blue-700 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back to Checklists
            </a>
        </div>

        <!-- Checklist Details Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <h3 class="text-lg font-semibold mb-4">Checklist Details</h3>

            <form action="{{ route('mobile.checklists.update', $checklist->id) }}" method="POST">
                @csrf

                <div class="mb-4">
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

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $checklist->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors text-sm">
                        Update Checklist
                    </button>
                </div>
            </form>
        </div>

        <!-- Checklist Usage Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>How to Use This Checklist
            </h3>
            <div class="text-sm text-blue-700 space-y-2">
                <p><strong>Adding Items:</strong> Click the "+ Add Item" button or press Enter in any description field to quickly add new items.</p>
                <p><strong>Item Types:</strong> Choose between "Check" (checkbox for completion) or "Text" (text input for responses).</p>
                <p><strong>Required Items:</strong> Check the "Required" box for items that must be completed before marking a request as finished.</p>
                <p><strong>Editing:</strong> Click the yellow edit button to save changes to existing items.</p>
                <p><strong>Deleting:</strong> Click the red X button to remove items (existing items will be permanently deleted).</p>
                <p><strong>Search:</strong> Use the search bar to quickly find specific checklist items.</p>
            </div>
        </div>

        <!-- Dynamic Checklist Items Editor -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
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
                           class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                <div class="checklist-item-row border border-gray-200 rounded-lg p-3 bg-gray-50" data-item-id="new">
                    <div class="flex items-center space-x-2">
                        <!-- Type Dropdown -->
                        <div class="flex-shrink-0 w-20">
                            <select class="item-type w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="checkbox">Check</option>
                                <option value="text">Text</option>
                            </select>
                        </div>
                        
                        <!-- Description Field -->
                        <div class="flex-1">
                            <input type="text" 
                                   class="item-description w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                   placeholder="Enter checklist item description..."
                                   onkeydown="handleKeyDown(event, this)">
                        </div>
                        
                        <!-- Required Checkbox -->
                        <div class="flex-shrink-0">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       class="item-required rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-1 text-xs text-gray-600">Required</span>
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

function handleKeyDown(event, input) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.stopPropagation();
        
        const row = input.closest('.checklist-item-row');
        const saveBtn = row.querySelector('.save-item-btn');
        if (saveBtn) {
            saveItem(saveBtn);
        }
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
    
    fetch('{{ route("mobile.checklists.items.store", $checklist) }}', {
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
    
    fetch(`{{ route('mobile.checklists.items.update', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
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
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-edit"></i>';
            showNotification('Item updated successfully!', 'success');
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
            
            fetch(`{{ route('mobile.checklists.items.destroy', [$checklist, 'ITEM_ID']) }}`.replace('ITEM_ID', itemId), {
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
                    row.remove();
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
</script>
@endsection 