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
                @method('PUT')

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

        <!-- Add New Item Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <h3 class="text-lg font-semibold mb-4">Add New Checklist Item</h3>

            <form action="{{ route('mobile.checklists.items.store', $checklist->id) }}" method="POST" enctype="multipart/form-data" id="addItemForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select name="type" 
                                id="type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="">Select type</option>
                            <option value="text">Text</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <input type="text" 
                               name="description" 
                               id="itemDescription" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter item description"
                               required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Attachment</label>
                    <input type="file" 
                           name="attachment" 
                           id="attachment" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           accept="image/*,.pdf,.doc,.docx">
                </div>

                <div class="mb-4" id="requiredField" style="display: none;">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_required" 
                               value="1"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Required (for checkbox items)</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            id="addItemBtn"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Add Item
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Items -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-lg font-semibold mb-4">Checklist Items ({{ $checklist->items->count() }})</h3>

            @if($checklist->items->count() > 0)
                <div class="space-y-4">
                    @foreach($checklist->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4">
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
                                    <form method="POST" 
                                          action="{{ route('mobile.checklists.items.destroy', [$checklist->id, $item->id]) }}" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No items added yet. Add your first checklist item above.</p>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const requiredField = document.getElementById('requiredField');
    const addItemBtn = document.getElementById('addItemBtn');
    const itemDescription = document.getElementById('itemDescription');

    // Show/hide required field based on type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'checkbox') {
            requiredField.style.display = 'block';
        } else {
            requiredField.style.display = 'none';
        }
        checkFormValidity();
    });

    // Check form validity
    function checkFormValidity() {
        const isValid = typeSelect.value && itemDescription.value.trim();
        addItemBtn.disabled = !isValid;
    }

    itemDescription.addEventListener('input', checkFormValidity);
    typeSelect.addEventListener('change', checkFormValidity);
    
    // Add Enter key support for quick item addition
    itemDescription.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !addItemBtn.disabled) {
            e.preventDefault();
            addItemBtn.click();
        }
    });
});

// Enhanced form submission with AJAX
document.getElementById('addItemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const addItemBtn = document.getElementById('addItemBtn');
    
    // Show loading state
    addItemBtn.disabled = true;
    addItemBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    fetch(this.action, {
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
            // Reset form
            this.reset();
            document.getElementById('requiredField').style.display = 'none';
            
            // Reload page to show new item (or implement dynamic addition)
            location.reload();
            
            showNotification('Item added successfully!', 'success');
        } else {
            throw new Error(data.message || 'Failed to add item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding item: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button
        addItemBtn.disabled = false;
        addItemBtn.innerHTML = 'Add Item';
    });
});

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