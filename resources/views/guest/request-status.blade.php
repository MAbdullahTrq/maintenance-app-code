@extends('layouts.guest')

@section('title', 'Request Status')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Maintenance Request Status</h2>
                <p class="text-gray-600 mt-2">for {{ $property->name }}</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-800">Request Details</h3>
                    <div>
                        @if($maintenanceRequest->status == 'pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($maintenanceRequest->status == 'approved')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Approved
                            </span>
                        @elseif($maintenanceRequest->status == 'in_progress')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                In Progress
                            </span>
                        @elseif($maintenanceRequest->status == 'completed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>
                        @elseif($maintenanceRequest->status == 'declined')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Declined
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Title</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $maintenanceRequest->title }}</dd>
                        </div>
                        
                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $maintenanceRequest->location }}</dd>
                        </div>
                        
                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $maintenanceRequest->description }}</dd>
                        </div>
                        
                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Submitted On</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $maintenanceRequest->created_at->format('F j, Y, g:i a') }}</dd>
                        </div>
                        
                        @if($maintenanceRequest->due_date)
                            <div class="py-3 grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ $maintenanceRequest->due_date->format('F j, Y') }}</dd>
                            </div>
                        @endif
                        
                        @if($maintenanceRequest->completed_at)
                            <div class="py-3 grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Completed On</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ $maintenanceRequest->completed_at->format('F j, Y, g:i a') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
            
            @if($maintenanceRequest->images->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Request Images</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($maintenanceRequest->images->where('type', 'request') as $image)
                            <div class="relative">
                                <img src="{{ $image->getUrl() }}" alt="Request Image" class="h-32 w-full object-cover rounded-lg">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @if($maintenanceRequest->isCompleted() && $maintenanceRequest->images->where('type', 'completion')->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Completion Images</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($maintenanceRequest->images->where('type', 'completion') as $image)
                            <div class="relative">
                                <img src="{{ $image->getUrl() }}" alt="Completion Image" class="h-32 w-full object-cover rounded-lg">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="flex justify-center">
                <a href="{{ route('guest.request.form', $property->access_link) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                    Submit Another Request
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 