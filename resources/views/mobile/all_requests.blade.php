@extends('mobile.layout')

@section('title', 'All Requests - Manager')

@section('header-actions')
<a href="{{ route('mobile.manager.dashboard') }}" class="text-sm font-medium">Back to Dashboard</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 w-full max-w-4xl mx-auto">
        <div class="text-center font-bold text-lg mb-2">All Requests</div>
        <div class="grid grid-cols-5 gap-0 mb-4 border border-gray-400 rounded overflow-hidden">
            <a href="?status=declined{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'declined') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs">Declined</div>
                <div class="text-lg font-bold">{{ $declinedCount }}</div>
            </a>
            <a href="?status=assigned{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'assigned') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs">Assigned</div>
                <div class="text-lg font-bold">{{ $assignedCount }}</div>
            </a>
            <a href="?status=accepted{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'accepted') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs">Accepted</div>
                <div class="text-lg font-bold">{{ $acceptedCount }}</div>
            </a>
            <a href="?status=started{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 border-r border-gray-400 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'started') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs">Started</div>
                <div class="text-lg font-bold">{{ $startedCount }}</div>
            </a>
            <a href="?status=completed{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('direction') ? '&direction=' . request('direction') : '' }}" class="text-center p-2 block focus:outline-none {{ (isset($selectedStatus) && $selectedStatus === 'completed') ? 'bg-blue-100 font-bold' : '' }}">
                <div class="font-semibold text-xs">Completed</div>
                <div class="text-lg font-bold">{{ $completedCount }}</div>
            </a>
        </div>
        @if(isset($selectedStatus) && $selectedStatus)
            <div class="mb-2 text-right">
                <a href="{{ route('mobile.manager.all-requests') }}{{ request('sort') ? '?sort=' . request('sort') : '' }}{{ request('direction') ? (request('sort') ? '&' : '?') . 'direction=' . request('direction') : '' }}" class="inline-block px-3 py-1 bg-gray-200 rounded text-xs font-semibold hover:bg-gray-300">Clear Filter</a>
            </div>
        @endif
        @if($allRequests->count())
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-1 md:p-2 border-r border-gray-400">Property</th>
                        <th class="p-1 md:p-2 border-r border-gray-400">Priority</th>
                        <th class="p-1 md:p-2 border-r border-gray-400">
                            <a href="?{{ request('status') ? 'status=' . request('status') . '&' : '' }}sort=created_at&direction={{ $sortBy === 'created_at' && $sortDirection === 'desc' ? 'asc' : 'desc' }}" class="flex items-center justify-center hover:text-blue-600">
                                Date
                                @if($sortBy === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'desc' ? '↓' : '↑' }}</span>
                                @else
                                    <span class="ml-1">↓</span>
                                @endif
                            </a>
                        </th>
                        <th class="p-1 md:p-2 border-r border-gray-400">Status</th>
                        <th class="p-1 md:p-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allRequests as $req)
                    <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('mobile.request.show', $req->id) }}'">
                        <td class="p-1 md:p-2 align-top border-r border-gray-400">
                            <span class="font-semibold">{{ $req->property->name }}</span><br>
                            <span class="text-gray-500 text-xs">{{ $req->property->address }}</span>
                        </td>
                        <td class="p-1 md:p-2 align-top border-r border-gray-400 {{ $req->priority == 'high' ? 'bg-red-500 text-white' : ($req->priority == 'low' ? 'bg-yellow-200' : ($req->priority == 'medium' ? 'bg-yellow-100' : '')) }}">
                            {{ ucfirst($req->priority) }}
                        </td>
                        <td class="p-1 md:p-2 align-top border-r border-gray-400">{{ \Carbon\Carbon::parse($req->created_at)->format('d M, Y') }}</td>
                        <td class="p-1 md:p-2 align-top border-r border-gray-400">{{ ucfirst($req->status) }}</td>
                        <td class="p-1 md:p-2 align-top">
                            <a href="{{ route('mobile.request.show', $req->id) }}" class="text-blue-600 hover:text-blue-800" onclick="event.stopPropagation();">
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
<a href="{{ route('mobile.requests.create') }}" class="fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center text-3xl shadow-lg z-50">
    +
</a>
@endsection 