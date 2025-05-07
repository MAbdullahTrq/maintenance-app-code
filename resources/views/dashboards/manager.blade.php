@extends('layouts.app')

@section('title', 'Property Manager Dashboard')
@section('header', 'Property Manager Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8 mb-16 md:mb-0">
    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $pendingRequests }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Assigned</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ $assignedRequests }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Started</h3>
            <p class="text-2xl font-bold text-green-600">{{ $startedRequests }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-700">Completed</h3>
            <p class="text-2xl font-bold text-purple-600">{{ $completedRequests }}</p>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Recent Maintenance Requests</h2>
        </div>
        <div class="overflow-x-auto">
            @foreach($recentRequests as $request)
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $request->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $request->property->name }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($request->status === 'pending') bg-blue-100 text-blue-800
                        @elseif($request->status === 'assigned') bg-yellow-100 text-yellow-800
                        @elseif($request->status === 'started') bg-green-100 text-green-800
                        @elseif($request->status === 'completed') bg-purple-100 text-purple-800
                        @endif">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $request->created_at->format('d M, Y') }}
                    </span>
                    <a href="{{ route('maintenance.show', $request) }}" 
                       class="btn-mobile-full md:btn-normal px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Properties Overview -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Properties Overview</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
            @foreach($properties as $property)
            <div class="border rounded-lg p-4">
                <div class="flex items-center mb-2">
                    @if($property->image)
                        <img src="{{ asset('storage/' . $property->image) }}" 
                             alt="{{ $property->name }}" 
                             class="w-16 h-16 rounded-lg object-cover mr-4">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-gray-200 mr-4 flex items-center justify-center">
                            <i class="fas fa-building text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $property->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $property->address }}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('properties.show', $property) }}" 
                       class="text-blue-500 hover:text-blue-600">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection 