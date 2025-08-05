@extends('layouts.guest')

@section('title', 'Request Status')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Maintenance Request Status</h2>
                <p class="text-gray-600">Owner: <span class="font-semibold">{{ $owner->display_name }}</span></p>
            </div>
            
            <!-- Request Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Request Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600"><strong>Title:</strong></p>
                        <p class="text-gray-800">{{ $maintenanceRequest->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600"><strong>Status:</strong></p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($maintenanceRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($maintenanceRequest->status === 'approved') bg-green-100 text-green-800
                            @elseif($maintenanceRequest->status === 'declined') bg-red-100 text-red-800
                            @elseif($maintenanceRequest->status === 'assigned') bg-blue-100 text-blue-800
                            @elseif($maintenanceRequest->status === 'accepted') bg-indigo-100 text-indigo-800
                            @elseif($maintenanceRequest->status === 'started') bg-purple-100 text-purple-800
                            @elseif($maintenanceRequest->status === 'completed') bg-green-100 text-green-800
                            @elseif($maintenanceRequest->status === 'closed') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($maintenanceRequest->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-600"><strong>Priority:</strong></p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($maintenanceRequest->priority === 'high') bg-red-100 text-red-800
                            @elseif($maintenanceRequest->priority === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($maintenanceRequest->priority) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-600"><strong>Created:</strong></p>
                        <p class="text-gray-800">{{ $maintenanceRequest->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-600"><strong>Description:</strong></p>
                        <p class="text-gray-800">{{ $maintenanceRequest->description }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-600"><strong>Location:</strong></p>
                        <p class="text-gray-800">{{ $maintenanceRequest->location }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Property Information -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">Property Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-blue-700"><strong>Property:</strong></p>
                        <p class="text-blue-800">{{ $maintenanceRequest->property->name }}</p>
                    </div>
                    <div>
                        <p class="text-blue-700"><strong>Address:</strong></p>
                        <p class="text-blue-800">{{ $maintenanceRequest->property->address }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Status Timeline -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Request Timeline</h3>
                <div class="space-y-4">
                    <!-- Created -->
                    <div class="flex items-start">
                        <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-0.5">
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">Request Submitted</p>
                            <p class="text-gray-600 text-sm">{{ $maintenanceRequest->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($maintenanceRequest->status !== 'pending')
                        <!-- Approved/Declined -->
                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-0.5">
                                <i class="fas fa-eye text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">Request Reviewed</p>
                                <p class="text-gray-600 text-sm">
                                    @if($maintenanceRequest->status === 'declined')
                                        Request was declined
                                    @else
                                        Request was approved
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                    
                    @if(in_array($maintenanceRequest->status, ['assigned', 'accepted', 'started', 'completed', 'closed']))
                        <!-- Assigned -->
                        <div class="flex items-start">
                            <div class="bg-purple-100 rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-0.5">
                                <i class="fas fa-user-cog text-purple-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">Technician Assigned</p>
                                <p class="text-gray-600 text-sm">
                                    @if($maintenanceRequest->assignedTo)
                                        Assigned to {{ $maintenanceRequest->assignedTo->name }}
                                    @else
                                        Technician assigned to handle the request
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                    
                    @if(in_array($maintenanceRequest->status, ['accepted', 'started', 'completed', 'closed']))
                        <!-- Accepted -->
                        <div class="flex items-start">
                            <div class="bg-indigo-100 rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-0.5">
                                <i class="fas fa-thumbs-up text-indigo-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">Work Accepted</p>
                                <p class="text-gray-600 text-sm">Technician has accepted the maintenance work</p>
                            </div>
                        </div>
                    @endif
                    
                    @if(in_array($maintenanceRequest->status, ['started', 'completed', 'closed']))
                        <!-- Started -->
                        <div class="flex items-start">
                            <div class="bg-orange-100 rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-0.5">
                                <i class="fas fa-tools text-orange-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">Work Started</p>
                                <p class="text-gray-600 text-sm">
                                    @if($maintenanceRequest->started_at)
                                        Started on {{ $maintenanceRequest->started_at->format('M j, Y g:i A') }}
                                    @else
                                        Maintenance work has begun
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                    
                    @if(in_array($maintenanceRequest->status, ['completed', 'closed']))
                        <!-- Completed -->
                        <div class="flex items-start">
                            <div class="bg-green-100 rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-0.5">
                                <i class="fas fa-check-circle text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">Work Completed</p>
                                <p class="text-gray-600 text-sm">Maintenance work has been completed</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($maintenanceRequest->status === 'closed')
                        <!-- Closed -->
                        <div class="flex items-start">
                            <div class="bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-0.5">
                                <i class="fas fa-lock text-gray-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">Request Closed</p>
                                <p class="text-gray-600 text-sm">Maintenance request has been closed</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Comments -->
            @if($maintenanceRequest->comments->count() > 0)
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Comments & Updates</h3>
                    <div class="space-y-4">
                        @foreach($maintenanceRequest->comments->sortBy('created_at') as $comment)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="font-medium text-gray-800">
                                        @if($comment->user)
                                            {{ $comment->user->name }}
                                        @else
                                            {{ $comment->requester_name ?: 'Anonymous' }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $comment->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                                <p class="text-gray-700">{{ $comment->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Images -->
            @if($maintenanceRequest->images->count() > 0)
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Request Images</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($maintenanceRequest->images as $image)
                            <div class="relative">
                                <img src="{{ Storage::url($image->image_path) }}" 
                                     alt="Request image" 
                                     class="w-full h-32 object-cover rounded-lg border">
                                <p class="text-xs text-gray-500 mt-1 text-center">{{ ucfirst($image->type) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('owner.request.form', $owner->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Submit New Request
                </a>
                
                <a href="{{ route('owner.info', $owner->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-home mr-2"></i>
                    Back to Owner Portal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 