@extends('mobile.layout')

@section('title', 'Properties')

@push('styles')
<style>
    .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #f9fafb;
        width: auto;
        min-width: 200px;
        box-sizing: border-box;
    }
    
    .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        outline: none;
        background-color: white;
        transform: translateY(-1px);
    }
    
    .form-select option {
        padding: 8px 12px;
        background-color: white;
    }
</style>
@endpush

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div>
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg md:text-xl lg:text-2xl">All Properties</div>
                
                <!-- Add Property Button -->
                @if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
                <a href="{{ route('mobile.properties.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>Add
                </a>
                @elseif(Auth::user()->isViewer())
                    <!-- Viewers see no add button -->
                @else
                <a href="{{ route('mobile.subscription.plans') }}" class="bg-gray-400 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium" title="Subscription required">
                    <i class="fas fa-lock mr-1"></i>Add
                </a>
                @endif
            </div>
            
            <!-- Owner Filter Dropdown - Moved down -->
            <div class="flex items-center gap-2 mb-4">
                <form method="GET" action="{{ route('mobile.properties.index') }}" id="ownerFilterForm">
                    <div class="flex items-center gap-2">
                        <label for="owner_id" class="text-sm font-medium text-gray-700">Filter by Owner:</label>
                        <select name="owner_id" id="owner_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Owners</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ $selectedOwnerId == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->displayName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
                @if($selectedOwnerId)
                    <a href="{{ route('mobile.properties.index') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">Clear</a>
                @endif
            </div>
            
            <!-- Property count display -->
            <div class="mb-4 text-sm text-gray-600">
                Showing {{ $properties->count() }} {{ Str::plural('property', $properties->count()) }}
                @if($selectedOwnerId)
                    for selected owner
                @endif
            </div>
            
            <div class="overflow-x-auto w-full" style="overflow: visible;">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded" style="overflow: visible;">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Name</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Address</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Owner</th>
                            <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                        <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('mobile.properties.show', $property->id) }}'">
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                <div class="flex items-center gap-2 md:gap-3">
                                    @if($property->image)
                                        <img src="{{ asset('storage/' . $property->image) }}" class="rounded-full w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 object-cover" alt="Property Image">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($property->name) }}&background=eee&color=555&size=48" class="rounded-full w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12" alt="Profile">
                                    @endif
                                    <a href="{{ route('mobile.properties.show', $property->id) }}" class="font-semibold text-blue-700 hover:text-blue-900 cursor-pointer">{{ $property->name }}</a>
                                </div>
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">{{ $property->address }}</td>
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                <div class="text-gray-700">{{ $property->owner->displayName ?? '-' }}</div>
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top text-center" style="position: relative; overflow: visible;" onclick="event.stopPropagation();">
                                <div class="relative" style="overflow: visible;">
                                    <button onclick="toggleDropdown(this)" class="px-2 py-1 text-gray-600 hover:text-gray-800 text-lg md:text-xl focus:outline-none dropdown-btn">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu absolute top-full mt-1 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-[9999] hidden" style="min-width: 10rem;">
                                        <div class="py-1">
                                            <a href="{{ route('mobile.properties.show', $property->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-eye mr-2 text-blue-500"></i>View
                                            </a>
                                            @if(!Auth::user()->isViewer())
                                            <a href="{{ route('mobile.properties.edit', $property->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-edit mr-2 text-green-500"></i>Edit
                                            </a>
                                            @endif
                                            <a href="{{ route('mobile.properties.qrcode', $property->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-qrcode mr-2 text-purple-500"></i>QR Code
                                            </a>
                                            @if($property->access_link)
                                            <a href="{{ route('guest.request.form', $property->access_link) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <i class="fas fa-link mr-2 text-orange-500"></i>Public Link
                                            </a>
                                            @endif
                                            @if(!Auth::user()->isViewer())
                                            <form action="{{ route('mobile.properties.destroy', $property->id) }}" method="POST" class="block" onsubmit="return confirm('Are you sure you want to delete this property?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                                    <i class="fas fa-trash-alt mr-2"></i>Delete
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDropdown(button) {
    // Close all other dropdowns first
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== button.nextElementSibling) {
            menu.classList.add('hidden');
        }
    });
    
    // Toggle the clicked dropdown
    const menu = button.nextElementSibling;
    const buttonRect = button.getBoundingClientRect();
    const menuHeight = 200; // Approximate height of the dropdown menu
    const windowHeight = window.innerHeight;
    const spaceBelow = windowHeight - buttonRect.bottom;
    const spaceAbove = buttonRect.top;
    
    // Position dropdown to the left of the button
    menu.style.position = 'fixed';
    menu.style.left = (buttonRect.left - 200) + 'px'; // Position 200px to the left of button
    menu.style.zIndex = '9999';
    
    // Check if there's enough space below, if not, open upwards
    if (spaceBelow >= menuHeight || spaceBelow > spaceAbove) {
        // Open downwards
        menu.style.top = (buttonRect.bottom + 2) + 'px';
        menu.style.bottom = 'auto';
    } else {
        // Open upwards
        menu.style.bottom = (windowHeight - buttonRect.top + 2) + 'px';
        menu.style.top = 'auto';
    }
    
    menu.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

// Prevent dropdown from closing when clicking inside it
document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});
</script>
@endpush