@extends('mobile.layout')

@section('title', 'Owners')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div x-data="{ showForm: false, search: '', dropdownOpen: false, dropdownTop: 0, dropdownLeft: 0, dropdownOwner: null, showDeleteConfirm: false, deleteForm: null }" x-init="dropdownOwner = null">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg md:text-xl lg:text-2xl">All Owners</div>
            </div>
            <input type="text" x-model="search" placeholder="Search" class="w-full border rounded p-2 md:p-3 lg:p-4 mb-4 text-sm md:text-base" />
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Owner</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Contact</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Company</th>
                            <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($owners as $owner)
                        <tr x-show="search === '' || '{{ strtolower($owner->name . ' ' . $owner->email . ' ' . $owner->company) }}'.includes(search.toLowerCase())" class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='/m/ao/{{ $owner->id }}'">
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                <div class="font-semibold">{{ $owner->name }}</div>
                                <div class="text-gray-500 text-xs md:text-sm">{{ $owner->email }}</div>
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                <div class="text-gray-700">{{ $owner->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                <div class="text-gray-700">{{ $owner->company ?? 'N/A' }}</div>
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top text-center relative" onclick="event.stopPropagation();">
                                <button onclick="toggleDropdown(this, {{ $owner->id }})" class="px-2 py-1 text-gray-600 hover:text-gray-800 text-lg md:text-xl focus:outline-none dropdown-btn">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="dropdown-{{ $owner->id }}" class="dropdown-menu absolute right-0 top-full mt-1 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-[9999] hidden">
                                    <div class="py-1">
                                        <a href="/m/ao/{{ $owner->id }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-eye mr-2 text-blue-500"></i>View
                                        </a>
                                        @if(!Auth::user()->isViewer())
                                        <a href="/m/ao/{{ $owner->id }}/edit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-edit mr-2 text-green-500"></i>Edit
                                        </a>
                                        <form action="{{ route('mobile.owners.destroy', $owner->id) }}" method="POST" class="block" onsubmit="return confirm('Are you sure you want to delete this owner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-center block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-trash-alt mr-2"></i>Delete
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($owners->hasPages())
                <div class="mt-4">
                    {{ $owners->links() }}
                </div>
            @endif
            
            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteConfirm" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                <div class="bg-white rounded-lg p-6 max-w-sm mx-4" @click.away="showDeleteConfirm = false">
                    <h3 class="text-lg font-bold mb-2">Confirm Delete</h3>
                    <p class="text-gray-600 mb-4">Are you sure you want to delete this owner? This action cannot be undone.</p>
                    <div class="flex justify-end space-x-2">
                        <button @click="showDeleteConfirm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                        <button @click="deleteForm.submit(); showDeleteConfirm = false" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
<a href="/m/ao/create" class="fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center text-3xl shadow-lg z-50">
    <i class="fas fa-plus"></i>
</a>
@endif

<script>
function toggleDropdown(button, ownerId) {
    // Close all other dropdowns first
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== `dropdown-${ownerId}`) {
            menu.classList.add('hidden');
        }
    });
    
    const dropdown = document.getElementById(`dropdown-${ownerId}`);
    const buttonRect = button.getBoundingClientRect();
    
    // Position dropdown exactly at the button location
    dropdown.style.position = 'fixed';
    dropdown.style.top = (buttonRect.bottom + 2) + 'px';
    dropdown.style.left = (buttonRect.left - 160) + 'px'; // Position to the left of button
    dropdown.style.zIndex = '9999';
    
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-btn') && !event.target.closest('.dropdown-menu')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
</script>
@endsection 