@extends('layouts.app')

@section('title', 'Technician Dashboard')
@section('header', 'Technician Dashboard')

@section('content')
<div class="container mx-auto px-4 pt-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                    <i class="fas fa-clipboard-list text-blue-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Assigned Tasks</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalAssignedRequests }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                    <i class="fas fa-spinner text-purple-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">In Progress</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $inProgressRequests }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $completedRequests }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Tasks</h2>
            
            @if($upcomingRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingRequests as $request)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-md font-medium text-gray-900">{{ $request->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $request->property->name }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> {{ $request->location }}
                                    </p>
                                    @if($request->due_date)
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-calendar mr-1 text-blue-500"></i> Due: {{ $request->due_date->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('maintenance.show', $request) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View
                                    </a>
                                    
                                    @if($request->status === 'assigned')
                                    <form action="{{ route('maintenance.accept', $request) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Accept
                                        </button>
                                    </form>
                                    <button type="button" onclick="showDeclineModal({{ $request->id }})" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Decline
                                    </button>
                                    @endif
                                    
                                    @if($request->status === 'assigned' || $request->status === 'acknowledged')
                                    <form action="{{ route('maintenance.start-task', $request) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            Start
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($request->status === 'started')
                                    <form action="{{ route('maintenance.finish-task', $request) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            Finish
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('maintenance.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View All Tasks</a>
                </div>
            @else
                <p class="text-gray-500">No upcoming tasks found.</p>
            @endif
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">In Progress Tasks</h2>
            
            @if($inProgressTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($inProgressTasks as $request)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-md font-medium text-gray-900">{{ $request->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $request->property->name }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> {{ $request->location }}
                                    </p>
                                    @if($request->due_date)
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-calendar mr-1 text-blue-500"></i> Due: {{ $request->due_date->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('maintenance.show', $request) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Update
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('maintenance.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View All Tasks</a>
                </div>
            @else
                <p class="text-gray-500">No tasks in progress.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Task Completion Rate</h2>
        
        <div class="h-10 w-full bg-gray-200 rounded-full overflow-hidden">
            @php
                $completionRate = $totalAssignedRequests > 0 ? ($completedRequests / $totalAssignedRequests * 100) : 0;
            @endphp
            <div class="h-full bg-green-500 text-xs font-medium text-white text-center p-2 leading-none rounded-full" style="width: {{ $completionRate }}%">
                {{ round($completionRate) }}%
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Assigned/Acknowledged</p>
                <p class="text-xl font-semibold text-yellow-500">{{ $pendingRequests }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Started</p>
                <p class="text-xl font-semibold text-purple-500">{{ $inProgressRequests }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Completed</p>
                <p class="text-xl font-semibold text-green-500">{{ $completedRequests }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Decline Task Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 overflow-auto bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Decline Task</h3>
            <button type="button" onclick="hideDeclineModal()" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="declineForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="comment" class="block text-sm font-medium text-gray-700">Reason for declining</label>
                <textarea id="comment" name="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="button" onclick="hideDeclineModal()" class="mr-2 inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                    Decline
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentRequestId = null;
    
    function showDeclineModal(requestId) {
        currentRequestId = requestId;
        document.getElementById('declineForm').action = `/maintenance/${requestId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    
    function hideDeclineModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection 