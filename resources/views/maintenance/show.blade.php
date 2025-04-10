@extends('layouts.app')

@section('title', 'Maintenance Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('maintenance.index') }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Maintenance Requests
        </a>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
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
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            @if($maintenance->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($maintenance->status == 'approved') bg-blue-100 text-blue-800
                            @elseif($maintenance->status == 'in_progress') bg-purple-100 text-purple-800
                            @elseif($maintenance->status == 'completed') bg-green-100 text-green-800
                            @elseif($maintenance->status == 'declined') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $maintenance->description }}</p>
                    </div>
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Location</h2>
                        <p class="text-gray-700">{{ $maintenance->location }}</p>
                    </div>
                    
                    @if($maintenance->images && $maintenance->images->where('type', 'initial')->count() > 0)
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Images</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($maintenance->images->where('type', 'initial') as $image)
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
                        <p class="text-gray-900">{{ ucfirst($maintenance->priority ?? 'Normal') }}</p>
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
            
            <!-- Action Buttons -->
            @if($maintenance->status == 'pending' && (auth()->user()->isPropertyManager() || auth()->user()->isAdmin()))
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Actions</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex space-x-3 mb-4">
                            <form action="{{ route('maintenance.approve', $maintenance) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                    Approve
                                </button>
                            </form>
                            
                            <form action="{{ route('maintenance.decline', $maintenance) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                    Decline
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($maintenance->status == 'approved' && (auth()->user()->isPropertyManager() || auth()->user()->isAdmin()))
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Assign Technician</h2>
                    </div>
                    
                    <div class="p-6">
                        @if ($errors->any())
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                <p class="font-bold">Validation errors:</p>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('maintenance.assign', $maintenance) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assign to Technician</label>
                                <select name="assigned_to" id="assigned_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select a Technician</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Assign
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            
            @if($maintenance->status == 'pending' && auth()->user()->role == 'technician')
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Task Actions</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex space-x-4">
                            <form action="{{ route('maintenance.accept', $maintenance) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">Accept</button>
                            </form>
                            <form action="{{ route('maintenance.reject', $maintenance) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="text" name="comment" placeholder="Reason for rejection" required>
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($maintenance->status == 'approved' && auth()->user()->id == $maintenance->assigned_to)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Task Actions</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex space-x-4">
                            @if($maintenance->status == 'approved' && auth()->user()->id == $maintenance->assigned_to)
                                <form action="{{ route('maintenance.inProgress', $maintenance) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">Mark as Started</button>
                                </form>
                            @endif
                            
                            @if($maintenance->status == 'in_progress' && auth()->user()->id == $maintenance->assigned_to)
                                <form action="{{ route('maintenance.complete', $maintenance) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="text" name="comment" placeholder="Completion notes" required>
                                    <button type="submit" class="btn btn-primary">Mark as Completed</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 