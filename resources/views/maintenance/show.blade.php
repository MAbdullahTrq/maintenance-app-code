@extends('layouts.app')

@section('title', 'Maintenance Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('maintenance.index') }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Maintenance Requests
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $maintenance->title }}</h1>
                            <p class="text-sm text-gray-500 mt-1">Submitted on {{ $maintenance->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold 
                            @if($maintenance->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($maintenance->status == 'accepted') bg-blue-100 text-blue-800
                            @elseif($maintenance->status == 'assigned') bg-purple-100 text-purple-800
                            @elseif($maintenance->status == 'started') bg-indigo-100 text-indigo-800
                            @elseif($maintenance->status == 'completed') bg-green-100 text-green-800
                            @elseif($maintenance->status == 'declined') bg-red-100 text-red-800
                            @endif">
                            @if($maintenance->status == 'pending')
                                Pending
                            @elseif($maintenance->status == 'accepted')
                                Approved
                            @elseif($maintenance->status == 'assigned')
                                Assigned
                            @elseif($maintenance->status == 'acknowledged')
                                Accepted
                            @elseif($maintenance->status == 'started')
                                Started
                            @elseif($maintenance->status == 'completed')
                                Completed
                            @elseif($maintenance->status == 'declined')
                                Declined
                            @elseif($maintenance->status == 'closed')
                                Closed
                            @else
                                {{ ucfirst($maintenance->status) }}
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
                        @if($maintenance->checklist && (auth()->user()->isTechnician() || auth()->user()->isPropertyManager() || auth()->user()->hasTeamMemberRole()))
                            <!-- Checklist Items as Interactive Checkboxes for Technicians, Managers, and Team Members -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="font-medium text-gray-900 text-base mb-3">Checklist Items:</div>
                                <div class="space-y-3">
                                    @foreach($maintenance->checklist->items as $item)
                                        @php
                                            $response = $maintenance->checklistResponses()->where('checklist_item_id', $item->id)->first();
                                            $isCompleted = $response ? $response->is_completed : false;
                                        @endphp
                                        <div class="flex items-start space-x-3">
                                            @if($item->type === 'checkbox')
                                                <div class="flex-shrink-0 mt-1">
                                                    <input type="checkbox" 
                                                           id="item_{{ $item->id }}" 
                                                           class="checklist-item-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                           data-item-id="{{ $item->id }}"
                                                           data-request-id="{{ $maintenance->id }}"
                                                                                                      {{ $isCompleted ? 'checked' : '' }}
                                                                                              {{ ($maintenance->status === 'completed' || (auth()->user()->isTechnician() && $maintenance->status !== 'started')) ? 'disabled' : '' }}>
                                                </div>
                                                <div class="flex-1">
                                                    <label for="item_{{ $item->id }}" class="text-sm font-medium text-gray-900 {{ $isCompleted ? 'line-through text-gray-500' : '' }}">
                                                        {{ $item->description }}
                                                        @if($item->is_required)
                                                            <span class="text-red-500 ml-1">*</span>
                                                        @endif
                                                    </label>
                                                    @if($item->attachment_path)
                                                        <div class="mt-1">
                                                            <a href="{{ $item->attachment_url }}" 
                                                               target="_blank"
                                                               class="text-xs text-blue-600 hover:text-blue-800">
                                                                <i class="fas fa-paperclip mr-1"></i>View Attachment
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <!-- Text items - no checkbox, just display the text -->
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->description }}
                                                        @if($item->is_required)
                                                            <span class="text-red-500 ml-1">*</span>
                                                        @endif
                                                    </div>
                                                    @if($item->attachment_path)
                                                        <div class="mt-1">
                                                            <a href="{{ $item->attachment_url }}" 
                                                               target="_blank"
                                                               class="text-xs text-blue-600 hover:text-blue-800">
                                                                <i class="fas fa-paperclip mr-1"></i>View Attachment
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    <span class="text-red-500">*</span> Required checkbox items must be completed
                                                                @if(auth()->user()->isTechnician() && $maintenance->status !== 'started' && $maintenance->status !== 'completed')
                                <br><span class="text-orange-600">⚠️ Checklist items will become available after you start this job</span>
                            @endif
                                </div>
                            </div>
                        @else
                            <!-- Regular Description for non-checklist requests -->
                            <p class="text-gray-700 whitespace-pre-line">{{ $maintenance->description }}</p>
                        @endif
                    </div>
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Location</h2>
                        <p class="text-gray-700">{{ $maintenance->location }}</p>
                    </div>
                    
                    @if($maintenance->images && $maintenance->images->where('type', 'request')->count() > 0)
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Images</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($maintenance->images->where('type', 'request') as $image)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Maintenance Image" class="w-full h-40 object-cover rounded-lg">
                                        
                                        @if(auth()->user()->isPropertyManager() || auth()->user()->isAdmin())
                                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <form action="{{ route('maintenance.image.delete', $image) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg" onclick="return confirm('Are you sure you want to delete this image?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($maintenance->images && $maintenance->images->where('type', 'completion')->count() > 0)
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Completion Images</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($maintenance->images->where('type', 'completion') as $image)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Completion Image" class="w-full h-40 object-cover rounded-lg">
                                        
                                        @if(auth()->user()->isPropertyManager() || auth()->user()->isAdmin())
                                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <form action="{{ route('maintenance.image.delete', $image) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg" onclick="return confirm('Are you sure you want to delete this image?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Comments Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-900">Comments</h2>
                </div>
                
                <div class="p-6">
                    @if($maintenance->comments && $maintenance->comments->count() > 0)
                        <div class="space-y-6 mb-6">
                            @foreach($maintenance->comments as $comment)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y \a\t h:i A') }}</p>
                                        </div>
                                        
                                        @if($comment->user_id == auth()->id())
                                            <form action="{{ route('maintenance.comment.delete', $comment) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Are you sure you want to delete this comment?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    
                                    <p class="text-gray-700 whitespace-pre-line">{{ $comment->comment }}</p>
                                    
                                    @if($comment->images && $comment->images->count() > 0)
                                        <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
                                            @foreach($comment->images as $image)
                                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Comment Image" class="w-full h-24 object-cover rounded-lg">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 mb-6">No comments yet.</p>
                    @endif
                    
                    <form action="{{ route('maintenance.comment', $maintenance) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Add a Comment</label>
                            <textarea id="comment" name="comment" rows="3" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Type your comment here..."></textarea>
                            @error('comment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Attach Images (Optional)</label>
                            <input type="file" id="images" name="images[]" multiple 
                                class="w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('images.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Add Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-900">Request Details</h2>
                </div>
                
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Property</h3>
                        <p class="text-gray-900">{{ $maintenance->property->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Requester</h3>
                        <p class="text-gray-900">{{ $maintenance->requester_name ?? auth()->user()->name }}</p>
                        @if($maintenance->requester_email)
                            <p class="text-gray-500 text-sm">{{ $maintenance->requester_email }}</p>
                        @endif
                        @if($maintenance->requester_phone)
                            <p class="text-gray-500 text-sm">{{ $maintenance->requester_phone }}</p>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Priority</h3>
                        <div class="mt-1">
                            @if($maintenance->priority)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($maintenance->priority == 'low') bg-blue-100 text-blue-800
                                    @elseif($maintenance->priority == 'medium') bg-yellow-100 text-yellow-800
                                    @elseif($maintenance->priority == 'high') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ strtoupper($maintenance->priority ?: 'MEDIUM') }}
                                </span>
                                <p class="mt-1 text-sm text-gray-600">
                                    @if($maintenance->priority == 'low')
                                        You can fix after we leave, just wanted to let you know.
                                    @elseif($maintenance->priority == 'medium' || !$maintenance->priority)
                                        You can fix the next cleaning day is fine.
                                    @elseif($maintenance->priority == 'high')
                                        Fix asap please.
                                    @endif
                                </p>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    MEDIUM
                                </span>
                                <p class="mt-1 text-sm text-gray-600">
                                    You can fix the next cleaning day is fine.
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Assigned Technician</h3>
                        <p class="text-gray-900">{{ $maintenance->assignedTechnician ? $maintenance->assignedTechnician->name : 'Not Assigned' }}</p>
                    </div>
                    
                    @if($maintenance->due_date)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500">Due Date</h3>
                            <p class="text-gray-900">{{ $maintenance->due_date->format('M d, Y') }}</p>
                        </div>
                    @endif
                    
                    @if($maintenance->completed_at)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500">Completed On</h3>
                            <p class="text-gray-900">{{ $maintenance->completed_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Status Badge -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-900">Request Status</h2>
                </div>
                <div class="p-6">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($maintenance->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($maintenance->status == 'accepted') bg-blue-100 text-blue-800
                        @elseif($maintenance->status == 'assigned') bg-purple-100 text-purple-800
                        @elseif($maintenance->status == 'started') bg-indigo-100 text-indigo-800
                        @elseif($maintenance->status == 'completed') bg-green-100 text-green-800
                        @elseif($maintenance->status == 'declined') bg-red-100 text-red-800
                        @endif">
                        @if($maintenance->status == 'pending')
                            Pending
                        @elseif($maintenance->status == 'accepted')
                            Approved
                        @elseif($maintenance->status == 'assigned')
                            Assigned
                        @elseif($maintenance->status == 'acknowledged')
                            Accepted
                        @elseif($maintenance->status == 'started')
                            Started
                        @elseif($maintenance->status == 'completed')
                            Completed
                        @elseif($maintenance->status == 'declined')
                            Declined
                        @elseif($maintenance->status == 'closed')
                            Closed
                        @else
                            {{ ucfirst($maintenance->status) }}
                        @endif
                    </span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            @if($maintenance->status == 'pending' && auth()->user()->isPropertyManager())
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Request Actions</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex space-x-4">
                            <form action="{{ route('maintenance.approve', $maintenance) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                    Approve Request
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('declineModal').classList.remove('hidden')" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                Decline Request
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Delete Button for Property Managers (All Statuses) -->
            @if(auth()->user()->isPropertyManager())
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Delete Request</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('maintenance.destroy', $maintenance) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                                Delete Request
                            </button>
                        </form>
                    </div>
                </div>
            @endif

                <!-- Decline Modal -->
                <div id="declineModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Decline Request</h3>
                            <form action="{{ route('maintenance.decline', $maintenance) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Reason for Declining</label>
                                    <textarea name="comment" id="comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="document.getElementById('declineModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                        Decline
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if($maintenance->status == 'accepted' && (auth()->user()->isPropertyManager() || auth()->user()->isAdmin()))
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Assign Technician</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('maintenance.assign', $maintenance) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Select Technician</label>
                                <select name="assigned_to" id="assigned_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="">Select a technician</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Assign Technician
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($maintenance->status == 'assigned' && auth()->user()->id == $maintenance->assigned_to)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Task Actions</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex space-x-4">
                            <form action="{{ route('maintenance.accept', $maintenance) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                    Accept
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('declineTaskModal').classList.remove('hidden')" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Decline Task Modal -->
                <div id="declineTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Decline Task</h3>
                        <form action="{{ route('maintenance.reject', $maintenance) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="decline_comment" class="block text-sm font-medium text-gray-700 mb-1">Reason for Declining</label>
                                <textarea name="comment" id="decline_comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" onclick="document.getElementById('declineTaskModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                    Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if($maintenance->status == 'acknowledged' && auth()->user()->id == $maintenance->assigned_to)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Start Work</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('maintenance.start-task', $maintenance) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                                Start Working
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($maintenance->status == 'started' && auth()->user()->id == $maintenance->assigned_to)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Complete Task</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('maintenance.finish-task', $maintenance) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label for="tech_comment" class="block text-sm font-medium text-gray-700 mb-1">Completion Notes</label>
                                <textarea name="comment" id="tech_comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="tech_images" class="block text-sm font-medium text-gray-700 mb-1">Attach Images (Optional)</label>
                                <input type="file" name="images[]" id="tech_images" multiple class="w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                Mark as Completed
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if(($maintenance->status != 'completed' && $maintenance->status != 'closed') && (auth()->user()->isPropertyManager() && $maintenance->property->manager_id == auth()->user()->id))
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Manager Actions</h2>
                    </div>
                    <div class="p-6">
                        <button type="button" onclick="document.getElementById('completeModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                            Mark as Completed
                        </button>
                    </div>
                </div>
            @endif

            @if($maintenance->status == 'completed' && auth()->user()->isPropertyManager())
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Close Request</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('maintenance.close', $maintenance) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                Close Request
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Complete Request Modal (for Property Managers) -->
@if(auth()->user()->isPropertyManager() && !$maintenance->isCompleted() && !$maintenance->isClosed())
<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Mark Request as Completed</h3>
            <form action="{{ route('maintenance.complete', $maintenance) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="completion_comment" class="block text-sm font-medium text-gray-700 mb-1">Completion Notes</label>
                    <textarea name="comment" id="completion_comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="completion_images" class="block text-sm font-medium text-gray-700 mb-1">Attach Images (Optional)</label>
                    <input type="file" name="images[]" id="completion_images" multiple class="w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('completeModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                        Complete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// Checklist checkbox functionality for desktop
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checklist-item-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.getAttribute('data-item-id');
            const requestId = this.getAttribute('data-request-id');
            const isChecked = this.checked;
            const label = this.closest('.flex').querySelector('label');
            
            // Update visual state immediately
            if (isChecked) {
                label.classList.add('line-through', 'text-gray-500');
            } else {
                label.classList.remove('line-through', 'text-gray-500');
            }
            
            // Send AJAX request to update the response
            fetch(`/maintenance/${requestId}/checklist/${itemId}/response`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    is_completed: isChecked
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success feedback
                    const feedback = document.createElement('div');
                    feedback.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                    feedback.textContent = isChecked ? 'Item completed!' : 'Item unchecked';
                    document.body.appendChild(feedback);
                    
                    setTimeout(() => {
                        feedback.remove();
                    }, 2000);
                } else {
                    // Revert checkbox state on error
                    this.checked = !isChecked;
                    if (isChecked) {
                        label.classList.remove('line-through', 'text-gray-500');
                    } else {
                        label.classList.add('line-through', 'text-gray-500');
                    }
                    
                    // Show error feedback
                    const feedback = document.createElement('div');
                    feedback.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
                    feedback.textContent = 'Error updating item';
                    document.body.appendChild(feedback);
                    
                    setTimeout(() => {
                        feedback.remove();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert checkbox state on error
                this.checked = !isChecked;
                if (isChecked) {
                    label.classList.remove('line-through', 'text-gray-500');
                } else {
                    label.classList.add('line-through', 'text-gray-500');
                }
            });
        });
    });
});
</script>
@endsection 