@extends('mobile.layout')

@section('title', 'View Property - Manager')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
    <div class="mb-2 flex items-center">
        <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
    </div>
    <h2 class="text-center text-xl md:text-2xl lg:text-3xl font-bold mb-2 md:mb-4 text-yellow-600">{{ $property->name }}</h2>
    <div class="flex gap-2 md:gap-3 lg:gap-4 mb-4">
        <a href="{{ route('mobile.properties.qrcode', $property->id) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-blue-200 transition text-center text-sm md:text-base">QR Code</a>
        <a href="{{ route('guest.request.form', $property->access_link) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-blue-200 transition text-center text-sm md:text-base">Link</a>
        <a href="{{ route('mobile.properties.edit', $property->id) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-blue-200 transition text-center text-sm md:text-base">Edit</a>
        <form action="{{ route('properties.destroy', $property->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this property?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full bg-red-100 text-red-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-red-200 transition text-center text-sm md:text-base">Delete</button>
        </form>
    </div>

    <!-- Property Details Section with Responsive Layout -->
    <div class="bg-white border rounded p-3 md:p-4 lg:p-6 mb-4 md:mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Property Image (Top on mobile, left on large screens) -->
            @if($property->image)
                <div class="lg:col-span-1 order-1 lg:order-1">
                    <div class="mb-2 lg:mb-0">
                        <!-- <span class="font-semibold text-sm md:text-base block mb-2">Property image</span> -->
                        <div class="w-full max-w-sm mx-auto lg:max-w-none lg:mx-0">
                            <img src="{{ asset('storage/' . $property->image) }}" alt="Property image" class="w-full h-48 md:h-56 lg:h-64 object-cover rounded shadow">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Property Details (Bottom on mobile, right on large screens) -->
            <div class="@if($property->image) lg:col-span-2 order-2 lg:order-2 @else lg:col-span-3 @endif">
                <div class="space-y-3 md:space-y-4">
                    <div>
                        <span class="font-bold text-lg md:text-xl lg:text-2xl">{{ $property->name }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-sm md:text-base">Property address</span><br>
                        <span class="text-sm md:text-base lg:text-lg">{{ $property->address }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-sm md:text-base">Owner details</span><br>
                        @if($property->owner)
                            <div class="mt-2 space-y-1">
                                <div class="text-sm md:text-base lg:text-lg">{{ $property->owner->name }}</div>
                                @if($property->owner->email)
                                    <div class="text-sm md:text-base">
                                        <a href="mailto:{{ $property->owner->email }}" class="text-blue-600 hover:text-blue-800">{{ $property->owner->email }}</a>
                                    </div>
                                @endif
                                @if($property->owner->phone)
                                    <div class="text-sm md:text-base">{{ $property->owner->phone }}</div>
                                @endif
                                @if($property->owner->company)
                                    <div class="text-gray-600 text-sm">({{ $property->owner->company }})</div>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400">No owner assigned</span>
                        @endif
                        @if(Auth::user()->hasActiveSubscription())
                            <div class="mt-2">
                                <button onclick="showChangeOwnerModal()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                                    Change Owner
                                </button>
                            </div>
                        @endif
                    </div>
                    @if($property->special_instructions)
                    <div>
                        <span class="font-bold text-sm md:text-base">Special instructions</span><br>
                        <span class="text-sm md:text-base lg:text-lg">{{ $property->special_instructions }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-center text-lg md:text-xl lg:text-2xl font-bold mb-2 md:mb-4">Maintenance requests</h3>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Title</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Date created</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Date completed</th>
                        <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($property->maintenanceRequests as $req)
                    <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='/m/r/{{ $req->id }}'">
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 font-semibold">{{ $req->title }}</td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            <div>{{ $req->created_at->format('d M, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $req->created_at->format('H:i') }}</div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            @if($req->completed_at)
                                <div>{{ $req->completed_at->format('d M, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $req->completed_at->format('H:i') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                            <a href="/m/r/{{ $req->id }}" class="text-blue-600 hover:text-blue-800 text-lg md:text-xl" onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
@if(Auth::user()->hasActiveSubscription())
<a href="{{ route('mobile.properties.create') }}" class="fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center text-3xl shadow-lg z-50">
@else
    <a href="{{ route('mobile.subscription.plans') }}" class="fixed bottom-6 right-6 bg-gray-400 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg z-50" title="Subscription required">
        <i class="fas fa-lock text-xl"></i>
    </a>
@endif
    +
</a>

<!-- Change Owner Modal -->
<div id="changeOwnerModal" class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full shadow-2xl border border-gray-200">
        <h3 class="text-lg font-bold mb-4">Change Property Owner</h3>
        
        <!-- Debug info -->
        <div class="text-sm text-gray-600 mb-2">
            Available owners: {{ $owners->count() }}
            @if($owners->count() == 0)
                <div class="text-red-600">No owners found. Please create an owner first.</div>
            @endif
        </div>
        
        <form id="changeOwnerForm" method="POST" action="/m/ap/{{ $property->id }}/edit">
            @csrf
            
            <!-- Hidden fields to satisfy validation -->
            <input type="hidden" name="name" value="{{ $property->name }}">
            <input type="hidden" name="address" value="{{ $property->address }}">
            <input type="hidden" name="special_instructions" value="{{ $property->special_instructions }}">
            
            <div class="mb-4">
                <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">Select Owner</label>
                <select name="owner_id" id="owner_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select an owner</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ $property->owner_id == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }}
                            @if($owner->company)
                                ({{ $owner->company }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('owner_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="hideChangeOwnerModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Update Owner
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showChangeOwnerModal() {
    document.getElementById('changeOwnerModal').classList.remove('hidden');
}

function hideChangeOwnerModal() {
    document.getElementById('changeOwnerModal').classList.add('hidden');
}

// Close modal when clicking outside or with Escape key
document.getElementById('changeOwnerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideChangeOwnerModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideChangeOwnerModal();
    }
});

// Add form submission debugging
document.getElementById('changeOwnerForm').addEventListener('submit', function(e) {
    console.log('Form submitted');
    console.log('Owner ID:', document.getElementById('owner_id').value);
    console.log('Form action:', this.action);
    console.log('Form method:', this.method);
    
    // Don't prevent default - let the form submit normally
    // e.preventDefault();
});
</script>
@endsection 