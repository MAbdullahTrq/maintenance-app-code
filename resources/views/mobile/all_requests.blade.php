@extends('mobile.layout')

@section('title', 'All Requests - Manager')

@section('header-actions')
<a href="{{ route('mobile.manager.dashboard') }}" class="text-sm font-medium">Back to Dashboard</a>
@endsection

@section('content')
<style>
/* Ensure dropdown positioning works correctly */
.dropdown-menu {
    transition: all 0.2s ease;
}

.dropdown-menu.top-full {
    top: 100% !important;
    bottom: auto !important;
}

.dropdown-menu.bottom-full {
    bottom: 100% !important;
    top: auto !important;
}

/* Ensure proper positioning */
.dropdown-menu.mt-2 {
    margin-top: 0.5rem !important;
}

.dropdown-menu.mb-2 {
    margin-bottom: 0.5rem !important;
}
</style>
<div class="flex justify-center" x-data="{
    openDropdownId: null,
    positionDropdown(event, dropdownId) {
        const button = event.target.closest('button');
        const rect = button.getBoundingClientRect();
        const dropdown = button.nextElementSibling;
        const viewportHeight = window.innerHeight;
        const dropdownHeight = 120; // Increased dropdown height estimate
        const spaceBelow = viewportHeight - rect.bottom;
        const spaceAbove = rect.top;
        
        // Remove all positioning classes first
        dropdown.classList.remove('mt-2', 'mb-2', 'bottom-full', 'top-full');
        
        // Add a small delay to ensure the dropdown is rendered
        setTimeout(() => {
            // Check if button is in the bottom 30% of viewport - if so, always position above
            const isInBottomThird = rect.bottom > (viewportHeight * 0.7);
            
            // Be more conservative - if space below is less than 150px OR in bottom third, position above
            if (spaceBelow >= 150 && !isInBottomThird) {
                // Position below (default)
                dropdown.classList.add('mt-2', 'top-full');
                console.log('Positioning dropdown below, space below:', spaceBelow, 'isInBottomThird:', isInBottomThird);
            } else {
                // Position above
                dropdown.classList.add('mb-2', 'bottom-full');
                console.log('Positioning dropdown above, space below:', spaceBelow, 'space above:', spaceAbove, 'isInBottomThird:', isInBottomThird);
            }
        }, 10);
    },
    toggleDropdown(dropdownId, event) {
        if (this.openDropdownId === dropdownId) {
            this.openDropdownId = null;
        } else {
            this.openDropdownId = dropdownId;
            this.positionDropdown(event, dropdownId);
        }
    },
    closeAllDropdowns() {
        this.openDropdownId = null;
    }
}" @click="closeAllDropdowns()">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto" @click.stop>
        <div class="flex justify-between items-center mb-4">
            <div class="font-bold text-lg md:text-xl lg:text-2xl">All Requests</div>
            <div class="flex items-center gap-2">
                @if(!Auth::user()->isViewer())
                <a href="{{ route('mobile.reports.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-xs md:text-sm font-medium">
                    📊 Create Report
                </a>
                <a href="{{ route('mobile.requests.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
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
        </div>
        <div class="grid grid-cols-5 gap-0 mb-4 border border-gray-400 rounded overflow-hidden">
            <a href="?status=pending{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 md:p-3 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'pending') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs md:text-sm">Pending</div>
                <div class="text-sm md:text-lg lg:text-xl font-bold">{{ $pendingCount }}</div>
            </a>
            <a href="?status=assigned{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 md:p-3 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'assigned') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs md:text-sm">Assigned</div>
                <div class="text-sm md:text-lg lg:text-xl font-bold">{{ $assignedCount }}</div>
            </a>
            <a href="?status=accepted{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 md:p-3 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'accepted') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs md:text-sm">Accepted</div>
                <div class="text-sm md:text-lg lg:text-xl font-bold">{{ $acceptedCount }}</div>
            </a>
            <a href="?status=started{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 md:p-3 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'started') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs md:text-sm">Started</div>
                <div class="text-sm md:text-lg lg:text-xl font-bold">{{ $startedCount }}</div>
            </a>
            <a href="?status=completed{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 md:p-3 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'completed') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs md:text-sm">Completed</div>
                <div class="text-sm md:text-lg lg:text-xl font-bold">{{ $completedCount }}</div>
            </a>
        </div>
        @if(isset($selectedStatus) && $selectedStatus)
            <div class="mb-2 text-right">
                <a href="{{ route('mobile.manager.all-requests') }}{{ request('sort') ? '?sort=' . request('sort') : '' }}{{ request('direction') ? (request('sort') ? '&' : '?') . 'direction=' . request('direction') : '' }}" class="inline-block px-3 py-1 bg-gray-200 rounded text-xs font-semibold hover:bg-gray-300">Clear Filter</a>
            </div>
        @endif
        @if($allRequests->count())
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden table-fixed">
                <colgroup>
                    <col class="w-1/5">
                    <col class="w-2/5">
                    <col class="w-1/6">
                    <col class="w-1/6">
                    <col class="w-1/6">
                    <col class="w-1/12">
                </colgroup>
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Request</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                            <a href="?{{ request('status') ? 'status=' . request('status') . '&' : '' }}sort=priority&direction={{ $sortBy === 'priority' && $sortDirection === 'desc' ? 'asc' : 'desc' }}" class="flex items-center justify-center hover:text-blue-600">
                                Priority
                                @if($sortBy === 'priority')
                                    <span class="ml-1">{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                @else
                                    <span class="ml-1">↓</span>
                                @endif
                            </a>
                        </th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                            <a href="?{{ request('status') ? 'status=' . request('status') . '&' : '' }}sort=created_at&direction={{ $sortBy === 'created_at' && $sortDirection === 'desc' ? 'asc' : 'desc' }}" class="flex items-center justify-center hover:text-blue-600">
                                Date
                                @if($sortBy === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                @else
                                    <span class="ml-1">↓</span>
                                @endif
                            </a>
                        </th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">Status</th>
                        <th class="p-2 md:p-3 lg:p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allRequests as $req)
                    <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="if (!event.target.closest('.actions-cell')) window.location.href='{{ route('mobile.request.show', $req->id) }}'">
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                            <span class="font-semibold">{{ $req->property->name }}</span><br>
                            <div class="text-gray-500 text-xs md:text-sm">
                                {{ $req->property->address }}
                            </div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                            <span class="font-bold text-black" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">{{ $req->title }}</span><br>
                            <span class="text-gray-700 text-xs md:text-sm" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $req->description }}</span>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center {{ $req->priority == 'high' ? 'bg-red-200' : ($req->priority == 'low' ? 'bg-blue-200' : ($req->priority == 'medium' ? 'bg-yellow-200' : '')) }}" style="{{ $req->priority == 'high' ? 'background-color: #fecaca;' : ($req->priority == 'low' ? 'background-color: #bfdbfe;' : ($req->priority == 'medium' ? 'background-color: #fde68a;' : '')) }}">
                            {{ ucfirst($req->priority) }}
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            <div>{{ \Carbon\Carbon::parse($req->created_at)->format('d M, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">{{ ucfirst($req->status) }}</td>
                        <td class="p-2 md:p-3 lg:p-4 align-top text-center actions-cell">
                            <div class="relative">
                                <button @click="toggleDropdown('dropdown-{{ $req->id }}', $event); event.stopPropagation();" class="text-gray-600 hover:text-gray-800 text-lg md:text-xl">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="openDropdownId === 'dropdown-{{ $req->id }}'" @click.stop class="dropdown-menu absolute right-0 w-32 bg-white rounded-md shadow-lg z-50 border">
                                    <a href="{{ route('mobile.request.show', $req->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.stopPropagation();">
                                        <i class="fas fa-eye w-4 mr-2"></i>View
                                    </a>
                                    @if(auth()->user()->isPropertyManager())
                                        <form action="{{ route('mobile.request.destroy', $req->id) }}" method="POST" onclick="event.stopPropagation();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this request?')">
                                                <i class="fas fa-trash w-4 mr-2"></i>Delete
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
        @else
        <div class="text-center text-gray-500 py-4">No requests found.</div>
        @endif
    </div>
</div>
@endsection 