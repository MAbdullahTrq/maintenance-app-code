@extends('mobile.layout')

@section('title', 'Maintenance Request')

@section('header-actions')
<a href="#" class="text-sm font-medium">Manager &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-3 md:p-4 lg:p-6 w-full max-w-6xl mx-auto">
        <div class="mb-2 flex items-center">
            <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        <div class="mb-4 md:mb-6 text-center">
            <span class="inline-block bg-gray-200 px-2 md:px-3 py-1 md:py-2 rounded text-xs md:text-sm font-semibold mb-1 md:mb-2">{{ ucfirst($request->status) }}</span>
            <div class="font-bold text-lg md:text-xl lg:text-2xl">Maintenance Request</div>
            <div class="text-xl md:text-xl text-gray-500">{{ $request->property->name }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="grid grid-cols-2 border border-gray-400 rounded mb-4 md:mb-6">
            <div class="py-2 md:py-3 lg:py-4 border-r border-gray-400 text-sm md:text-base lg:text-lg font-semibold {{ $request->priority == 'high' ? 'bg-red-500 text-white' : ($request->priority == 'low' ? 'bg-yellow-200' : ($request->priority == 'medium' ? 'bg-yellow-100' : '')) }} flex items-center justify-center">
                {{ ucfirst($request->priority) }}
            </div>
            <div class="py-2 md:py-3 lg:py-4 text-xs md:text-sm pl-3">
                <div class="mb-1"><span class="font-semibold">Created:</span> {{ \Carbon\Carbon::parse($request->created_at)->format('d M, Y H:i') }}</div>
                <div class="mb-1"><span class="font-semibold">Started:</span> {{ $request->started_at ? \Carbon\Carbon::parse($request->started_at)->format('d M, Y H:i') : '-' }}</div>
                <div><span class="font-semibold">Finished:</span> {{ $request->completed_at ? \Carbon\Carbon::parse($request->completed_at)->format('d M, Y H:i') : '-' }}</div>
            </div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4 md:mb-6">
            <div class="font-semibold text-sm md:text-base">Property name</div>
            <div class="text-sm md:text-base lg:text-lg">{{ $request->property->name }}</div>
            <div class="font-semibold mt-2 md:mt-3 text-sm md:text-base">Property address</div>
            <div class="text-sm md:text-base lg:text-lg">{{ $request->property->address }}</div>
            @if($request->property->special_instructions)
                <div class="font-semibold mt-2 md:mt-3 text-sm md:text-base">Special instructions</div>
                <div class="text-sm md:text-base lg:text-lg">{{ $request->property->special_instructions }}</div>
            @endif
        </div>
        <hr class="my-4 border-gray-300">
        @if($request->assignedTechnician)
        <div class="mb-4">
            <div class="font-semibold">Assigned Technician</div>
            <div>{{ $request->assignedTechnician->name }}</div>
            <div class="text-xs text-gray-500">{{ $request->assignedTechnician->email }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        @endif
        <div class="mb-4">
            <div class="font-semibold">Request title</div>
            <div>{{ $request->title }}</div>
            <div class="font-semibold mt-2">Description</div>
            @if($request->checklist && (auth()->user()->isTechnician() || auth()->user()->isPropertyManager() || auth()->user()->hasTeamMemberRole()))
                <!-- Checklist Items as Interactive Checkboxes for Technicians, Managers, and Team Members -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-2">
                    <div class="font-medium text-gray-900 text-sm mb-3">Checklist Items:</div>
                    <div class="space-y-2">
                        @foreach($request->checklist->items as $item)
                            @php
                                $response = $request->checklistResponses()->where('checklist_item_id', $item->id)->first();
                                $isCompleted = $response ? $response->is_completed : false;
                            @endphp
                            <div class="flex items-start space-x-2">
                                @if($item->type === 'checkbox')
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="checkbox" 
                                               id="mobile_item_{{ $item->id }}" 
                                               class="mobile-checklist-item-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               data-item-id="{{ $item->id }}"
                                               data-request-id="{{ $request->id }}"
                                               {{ $isCompleted ? 'checked' : '' }}
                                               {{ $request->status === 'completed' ? 'disabled' : '' }}>
                                    </div>
                                    <div class="flex-1">
                                        <label for="mobile_item_{{ $item->id }}" class="text-sm font-medium text-gray-900 {{ $isCompleted ? 'line-through text-gray-500' : '' }}">
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
                    <div class="mt-2 text-xs text-gray-500">
                        <span class="text-red-500">*</span> Required checkbox items must be completed
                    </div>
                </div>
            @else
                <!-- Regular Description for non-checklist requests -->
                <div>{{ $request->description }}</div>
            @endif
            <div class="font-semibold mt-2">Location</div>
            <div>{{ $request->location }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4 md:mb-6">
            <div class="font-bold text-lg md:text-xl lg:text-2xl mb-2 md:mb-3">Requester Info</div>
            <div class="mb-2"><span class="font-semibold text-sm md:text-base">Requester name:</span> <span class="text-sm md:text-base lg:text-lg">{{ $request->requester_name ?? 'Not provided' }}</span></div>
            <div class="mb-2"><span class="font-semibold text-sm md:text-base">Email:</span> <span class="text-sm md:text-base lg:text-lg">{{ $request->requester_email ?? 'Not provided' }}</span></div>
            <div class="mb-1"><span class="font-semibold text-sm md:text-base">Phone:</span> <span class="text-sm md:text-base lg:text-lg">{{ $request->requester_phone ?? 'Not provided' }}</span></div>
        </div>
        <hr class="my-4 border-gray-300">
        <!-- Images Section with Popup -->
        <div x-data="{ showModal: false, modalImage: '' }" class="mb-4 md:mb-6">
            <div class="flex flex-wrap gap-2 md:gap-3 mb-2">
                @foreach($request->images as $image)
                    <img 
                        src="{{ asset('storage/' . $image->image_path) }}" 
                        alt="Request Image" 
                        class="w-16 h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity"
                        @click="showModal = true; modalImage = '{{ asset('storage/' . $image->image_path) }}'"
                    >
                @endforeach
            </div>
            <!-- Modal -->
            <div 
                x-show="showModal" 
                x-transition 
                class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
                @click.self="showModal = false"
                style="display: none;"
            >
                <img :src="modalImage" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white">
            </div>
        </div>
        <hr class="my-4 border-gray-300">
        @if($request->comments && $request->comments->count() > 0)
        <div class="mb-4">
            <div class="font-semibold mb-1">Comments</div>
            <div class="space-y-2">
                @foreach($request->comments as $comment)
                    @php $isOwn = auth()->check() && $comment->user_id === auth()->id(); @endphp
                    <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                        <div class="rounded p-2 max-w-[75%] {{ $isOwn ? 'bg-blue-500 text-white text-right' : 'bg-gray-100 text-gray-900 text-left' }}">
                            <div class="text-xs {{ $isOwn ? 'text-blue-100' : 'text-gray-600' }} mb-1">
                                {{ $comment->user->name ?? 'User' }} &middot; {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}
                            </div>
                            <div class="text-sm mb-2">{{ $comment->comment }}</div>
                            
                            <!-- Display attached media -->
                            @if($comment->images && $comment->images->count() > 0)
                                <div class="mt-2 space-y-2">
                                    @foreach($comment->images as $media)
                                        @if($media->type === 'comment_image')
                                            <div class="relative">
                                                <img src="{{ asset('storage/' . $media->image_path) }}" 
                                                     alt="Comment attachment" 
                                                     class="max-w-full h-auto rounded cursor-pointer"
                                                     onclick="openMediaModal('{{ asset('storage/' . $media->image_path) }}', 'image')">
                                                <div class="absolute top-1 right-1 bg-black bg-opacity-50 rounded-full p-1">
                                                    <i class="fas fa-image text-white text-xs"></i>
                                                </div>
                                            </div>
                                        @elseif($media->type === 'comment_video')
                                            <div class="relative">
                                                <video controls class="max-w-full h-auto rounded">
                                                    <source src="{{ asset('storage/' . $media->image_path) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="absolute top-1 right-1 bg-black bg-opacity-50 rounded-full p-1">
                                                    <i class="fas fa-video text-white text-xs"></i>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        <hr class="my-4 border-gray-300">
        <form method="POST" action="{{ route('mobile.request.comment', $request->id) }}" class="mb-4" enctype="multipart/form-data" id="comment-form">
            @csrf
            <textarea name="comment" class="w-full border rounded p-2 mb-2" placeholder="Add a comment..." required></textarea>
            
            <!-- Media Upload Section -->
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">📎 Attach Media (Optional)</label>
                <input type="file" name="media[]" id="comment-media" class="w-full border rounded p-2 text-sm" 
                       multiple accept="image/*,video/*" onchange="previewMedia(this)">
                <div class="text-xs text-gray-500 mt-1">
                    Images: JPG, PNG, GIF (max 10MB) | Videos: MP4, MOV, AVI (max 50MB)
                </div>
                <div id="media-preview" class="mt-2 grid grid-cols-2 gap-2 hidden"></div>
            </div>
            
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Comment</button>
        </form>
        <hr class="my-4 border-gray-300">
        {{-- ACTIONS SECTION --}}
        <div class="mb-2">
            @if($request->status === 'pending' && auth()->user() && auth()->user()->canAssignTasks())
                <form method="POST" action="{{ route('mobile.request.approve', $request->id) }}" class="mb-2 flex flex-col gap-2" x-data="{ tech: '' }">
                    @csrf
                    <div class="mb-2">
                        <label class="block font-semibold mb-1">Assign Technician*</label>
                        <select name="technician_id" class="w-full border rounded p-2" x-model="tech">
                            <option value="">Select Technician</option>
                            @php
                                $managerId = auth()->user()->isPropertyManager() ? auth()->id() : auth()->user()->getWorkspaceOwner()->id;
                            @endphp
                            @foreach(App\Models\User::whereHas('role', function($q){$q->where('slug','technician');})->where('invited_by', $managerId)->get() as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="w-1/2 bg-green-500 text-white py-2 rounded" :disabled="!tech">Assign</button>
                        <button type="button" onclick="document.getElementById('declineModal').classList.remove('hidden')" class="w-1/2 bg-gray-300 text-black py-2 rounded">Decline</button>
                    </div>
                </form>
                <div id="declineModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <form method="POST" action="{{ route('mobile.request.decline', $request->id) }}" class="bg-white p-4 rounded shadow max-w-xs w-full">
                        @csrf
                        <div class="mb-2 font-semibold">Reason for Decline</div>
                        <textarea name="comment" class="w-full border rounded p-2 mb-2" required></textarea>
                        <div class="flex gap-2">
                            <button type="submit" class="w-1/2 bg-red-500 text-white py-2 rounded">Decline</button>
                            <button type="button" onclick="document.getElementById('declineModal').classList.add('hidden')" class="w-1/2 bg-gray-300 text-black py-2 rounded">Cancel</button>
                        </div>
                    </form>
                </div>
            @elseif($request->status === 'assigned' && auth()->user() && auth()->user()->isTechnician() && $request->assigned_to == auth()->id())
                <form method="POST" action="{{ route('mobile.request.accept', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Accept</button>
                </form>
            @elseif($request->status === 'accepted' && auth()->user() && auth()->user()->isTechnician() && $request->assigned_to == auth()->id())
                <form method="POST" action="{{ route('mobile.request.start', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Start</button>
                </form>
            @elseif($request->status === 'started' && auth()->user() && auth()->user()->isTechnician() && $request->assigned_to == auth()->id())
                <form method="POST" action="{{ route('mobile.request.finish', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Finish</button>
                </form>

            @endif
            {{-- Always show Mark as Complete if eligible --}}
            @if(auth()->user() && auth()->user()->canAssignTasks() && in_array($request->status, ['pending', 'assigned', 'started', 'acknowledged', 'accepted']))
            <form method="POST" action="{{ route('mobile.request.complete', $request->id) }}" class="mb-2">
                @csrf
                <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Mark as Complete</button>
            </form>
            @endif
            
            {{-- Delete button for Property Managers (All Statuses) --}}
            @if(auth()->user() && auth()->user()->isPropertyManager())
            <form method="POST" action="{{ route('mobile.request.destroy', $request->id) }}" class="mb-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded" onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                    Delete Request
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Media Modal -->
<div id="mediaModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-[90vw] max-h-[90vh]">
        <button onclick="closeMediaModal()" class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-2">
            <i class="fas fa-times"></i>
        </button>
        <div id="modalContent"></div>
    </div>
</div>

<script>
// Media preview functionality
function previewMedia(input) {
    const preview = document.getElementById('media-preview');
    preview.innerHTML = '';
    preview.classList.add('hidden');
    
    if (input.files && input.files.length > 0) {
        preview.classList.remove('hidden');
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            const isImage = file.type.startsWith('image/');
            const isVideo = file.type.startsWith('video/');
            
            const previewDiv = document.createElement('div');
            previewDiv.className = 'relative bg-gray-100 rounded p-2';
            
            if (isImage) {
                reader.onload = function(e) {
                    previewDiv.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-16 object-cover rounded">
                        <div class="text-xs mt-1 truncate">${file.name}</div>
                        <div class="absolute top-1 right-1 bg-blue-500 text-white rounded-full p-1">
                            <i class="fas fa-image text-xs"></i>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else if (isVideo) {
                previewDiv.innerHTML = `
                    <div class="w-full h-16 bg-gray-300 rounded flex items-center justify-center">
                        <i class="fas fa-video text-gray-600 text-2xl"></i>
                    </div>
                    <div class="text-xs mt-1 truncate">${file.name}</div>
                    <div class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1">
                        <i class="fas fa-video text-xs"></i>
                    </div>
                `;
            }
            
            preview.appendChild(previewDiv);
        });
    }
}

// Modal functionality
function openMediaModal(src, type) {
    const modal = document.getElementById('mediaModal');
    const content = document.getElementById('modalContent');
    
    if (type === 'image') {
        content.innerHTML = `<img src="${src}" class="max-w-full max-h-full rounded">`;
    } else if (type === 'video') {
        content.innerHTML = `<video controls class="max-w-full max-h-full rounded"><source src="${src}" type="video/mp4"></video>`;
    }
    
    modal.classList.remove('hidden');
}

function closeMediaModal() {
    document.getElementById('mediaModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMediaModal();
    }
});

// Mobile checklist checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileCheckboxes = document.querySelectorAll('.mobile-checklist-item-checkbox');
    
    mobileCheckboxes.forEach(checkbox => {
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
                    showMobileFeedback(isChecked ? 'Item completed!' : 'Item unchecked', 'success');
                } else {
                    // Revert checkbox state on error
                    this.checked = !isChecked;
                    if (isChecked) {
                        label.classList.remove('line-through', 'text-gray-500');
                    } else {
                        label.classList.add('line-through', 'text-gray-500');
                    }
                    showMobileFeedback('Error updating item', 'error');
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
                showMobileFeedback('Error updating item', 'error');
            });
        });
    });
});

// Mobile feedback function
function showMobileFeedback(message, type) {
    const feedback = document.createElement('div');
    feedback.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    feedback.textContent = message;
    document.body.appendChild(feedback);
    
    setTimeout(() => {
        feedback.remove();
    }, 2000);
}
</script>
@endsection 