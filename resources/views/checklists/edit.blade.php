@extends('layouts.app')

@section('title', 'Edit Checklist')
@section('header', 'Edit Checklist')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

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

        <!-- Add New Item Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Add New Checklist Item</h3>

            <form action="{{ route('checklists.items.store', $checklist) }}" method="POST" enctype="multipart/form-data" id="addItemForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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

                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Attachment</label>
                        <input type="file" 
                               name="attachment" 
                               id="attachment" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               accept="image/*,.pdf,.doc,.docx">
                    </div>
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
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Add Item
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Items -->
        <div class="bg-white rounded-lg shadow p-6">
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
                                    <button onclick="editItem({{ $item->id }})" 
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
                <p class="text-gray-500 text-center py-4">No items added yet. Add your first checklist item above.</p>
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
});

function editItem(itemId) {
    // This would need to be implemented with AJAX to fetch item data
    // For now, we'll show a simple alert
    alert('Edit functionality will be implemented with AJAX to fetch item data');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 