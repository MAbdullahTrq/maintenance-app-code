@extends('layouts.app')

@section('title', 'Technician Dashboard')
@section('header', 'Technician Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8 mb-16 md:mb-0">
    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Assigned</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ $assignedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Started</h3>
            <p class="text-2xl font-bold text-green-600">{{ $startedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Completed</h3>
            <p class="text-2xl font-bold text-purple-600">{{ $completedCount }}</p>
        </div>
    </div>

    <!-- Upcoming Tasks -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Upcoming Tasks</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($upcomingRequests as $request)
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $request->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $request->property->name }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($request->status === 'assigned') bg-yellow-100 text-yellow-800
                        @elseif($request->status === 'acknowledged') bg-blue-100 text-blue-800
                        @elseif($request->status === 'started') bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>
                <div class="flex flex-col space-y-2 mt-4">
                    @if($request->status === 'assigned')
                        <form action="{{ route('maintenance.accept', $request) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                Accept
                            </button>
                        </form>
                        <form action="{{ route('maintenance.reject', $request) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                Decline
                            </button>
                        </form>
                    @endif
                    
                    @if(in_array($request->status, ['assigned', 'acknowledged']))
                        <form action="{{ route('maintenance.start', $request) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                                Start Working
                            </button>
                        </form>
                    @endif
                    
                    @if($request->status === 'started')
                        <form action="{{ route('maintenance.finish', $request) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                                Mark as Complete
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('maintenance.show', $request) }}" 
                       class="block text-center bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200">
                        View Details
                    </a>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-gray-500">
                No upcoming tasks found.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Recent Activity</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentActivity as $activity)
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span class="flex h-8 w-8 rounded-full bg-blue-100 items-center justify-center">
                            <i class="fas fa-tools text-blue-500"></i>
                        </span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                        <p class="text-sm text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-gray-500">
                No recent activity found.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection 