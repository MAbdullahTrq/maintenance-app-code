@extends('mobile.layout')

@section('title', 'All Requests - Manager')

@section('header-actions')
<a href="{{ route('mobile.manager.dashboard') }}" class="text-sm font-medium">Back to Dashboard</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
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
            <a href="?status=declined{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 md:p-3 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'declined') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs md:text-sm">Declined</div>
                <div class="text-sm md:text-lg lg:text-xl font-bold">{{ $declinedCount }}</div>
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
                    <col class="w-2/5">
                    <col class="w-1/6">
                    <col class="w-1/6">
                    <col class="w-1/6">
                    <col class="w-1/12">
                </colgroup>
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
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
                    <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('mobile.request.show', $req->id) }}'">
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                            <span class="font-semibold">{{ $req->property->name }}</span><br>
                            <span class="text-gray-500 text-xs md:text-sm">
                                <span class="md:hidden">{{ Str::limit($req->property->address, 15) }}</span>
                                <span class="sm:hidden md:block">{{ Str::limit($req->property->address, 30) }}</span>
                            </span>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center {{ $req->priority == 'high' ? 'bg-red-500 text-white' : ($req->priority == 'low' ? 'bg-yellow-200' : ($req->priority == 'medium' ? 'bg-yellow-100' : '')) }}">
                            {{ ucfirst($req->priority) }}
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            <div>{{ \Carbon\Carbon::parse($req->created_at)->format('d M, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">{{ ucfirst($req->status) }}</td>
                        <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                            <a href="{{ route('mobile.request.show', $req->id) }}" class="text-blue-600 hover:text-blue-800 text-lg md:text-xl" onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                            </a>
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