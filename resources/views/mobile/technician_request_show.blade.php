@extends('mobile.layout')

@section('title', ucfirst($request->status) . ' Request – Technician')

@section('header-actions')
<a href="#" class="text-sm font-medium">Technician &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-6 w-full max-w-4xl mx-auto">
        <div class="mb-2">
            <a href="{{ route('mobile.technician.dashboard') }}" class="text-blue-700 text-sm hover:underline flex items-center mb-2"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        <div class="text-center mb-4">
            <span class="inline-block bg-gray-200 px-3 py-1 rounded text-xs font-semibold mb-2">{{ ucfirst($request->status) }}</span>
            <div class="font-bold text-2xl md:text-3xl mb-1">Maintenance Request</div>
            <div class="text-base text-gray-700">({{ $property->name ?? '' }})</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="flex items-center justify-center {{ strtolower($request->priority) == 'high' ? 'bg-red-500 text-white' : (strtolower($request->priority) == 'low' ? 'bg-yellow-200' : (strtolower($request->priority) == 'medium' ? 'bg-yellow-100' : 'bg-gray-100')) }} font-bold py-3 rounded text-lg">
                {{ ucfirst($request->priority) }}
            </div>
            <div class="text-xs text-left py-1">
                <div class="mb-1"><span class="font-semibold">Created:</span> {{ $request->created_at ? date('d M, Y H:i', strtotime($request->created_at)) : '-' }}</div>
                <div class="mb-1"><span class="font-semibold">Started:</span> {{ $request->started_at ? date('d M, Y H:i', strtotime($request->started_at)) : '-' }}</div>
                <div><span class="font-semibold">Finished:</span> {{ $request->completed_at ? date('d M, Y H:i', strtotime($request->completed_at)) : '-' }}</div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-4">
            @if($request->status === 'assigned')
                <form method="POST" action="{{ route('mobile.technician.request.decline', $request->id) }}">@csrf<button type="submit" class="w-full bg-gray-300 text-black py-2 rounded font-semibold">Decline</button></form>
                <form method="POST" action="{{ route('mobile.technician.request.accept', $request->id) }}">@csrf<button type="submit" class="w-full bg-green-500 text-white py-2 rounded font-semibold">Accept</button></form>
            @elseif($request->status === 'accepted' || $request->status === 'acknowledged')
                <form method="POST" action="{{ route('mobile.technician.request.start', $request->id) }}">@csrf<button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-semibold">Start</button></form>
            @elseif($request->status === 'started')
                <form method="POST" action="{{ route('mobile.technician.request.finish', $request->id) }}">@csrf<button type="submit" class="w-full bg-yellow-500 text-black py-2 rounded font-semibold">Finish</button></form>
            @endif
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4">
            <div class="font-semibold text-gray-600">Request title</div>
            <div class="font-medium text-gray-900 text-base mb-2">{{ $request->title ?? '' }}</div>
        </div>
        <div class="mb-4">
            <div class="font-semibold text-gray-600">Description</div>
            @if($request->checklist)
                <!-- Checklist Items as Interactive Checkboxes -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-2">
                    <div class="font-medium text-gray-900 text-base mb-3">Checklist Items:</div>
                                         <div class="space-y-3">
                         @foreach($request->checklist->items as $item)
                             @php
                                 $response = $request->checklistResponses()->where('checklist_item_id', $item->id)->first();
                                 $isCompleted = $response ? $response->is_completed : false;
                             @endphp
                             <div class="flex items-start space-x-3">
                                 @if($item->type === 'checkbox')
                                     <div class="flex-shrink-0 mt-1">
                                         <input type="checkbox" 
                                                id="item_{{ $item->id }}" 
                                                class="checklist-item-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                data-item-id="{{ $item->id }}"
                                                data-request-id="{{ $request->id }}"
                                                {{ $isCompleted ? 'checked' : '' }}
                                                {{ ($request->status === 'completed' || $request->status !== 'started') ? 'disabled' : '' }}>
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
                         @if($request->status !== 'started' && $request->status !== 'completed')
                             <br><span class="text-orange-600">⚠️ Checklist items will become available after you start this job</span>
                         @endif
                     </div>
                </div>
            @else
                <!-- Regular Description -->
                <div class="font-medium text-gray-900 text-base mb-2">{{ $request->description ?? '' }}</div>
            @endif
        </div>
        <div class="mb-4">
            <div class="font-semibold text-gray-600">Location</div>
            <div class="font-medium text-gray-900 text-base mb-2">{{ $request->location ?? '' }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4" x-data="{ showModal: false, modalImage: '' }">
            <div class="font-semibold text-gray-600">Images</div>
            <div class="flex gap-2 flex-wrap mb-2">
                @foreach($request->images as $img)
                    <img src="{{ asset('storage/' . $img->image_path) }}" class="w-24 h-16 object-cover rounded border cursor-pointer" alt="Request Image" @click="showModal = true; modalImage = '{{ asset('storage/' . $img->image_path) }}'">
                @endforeach
            </div>
            <!-- Modal for image preview -->
            <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50" @click.self="showModal = false" style="display: none;">
                <img :src="modalImage" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white cursor-pointer" @click="showModal = false">
            </div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4">
            <div class="font-bold text-lg mb-2 text-gray-800">Property Details</div>
            <div class="bg-white border rounded p-3 md:p-4 lg:p-6 mb-4 md:mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Property Image (Top on mobile, left on large screens) -->
                    @if($property->image)
                        <div class="lg:col-span-1 order-1 lg:order-1">
                            <div class="mb-2 lg:mb-0">
                                <div class="w-full max-w-sm mx-auto lg:max-w-none lg:mx-0">
                                    <img src="{{ asset('storage/' . $property->image) }}" alt="Property image" class="w-full h-48 md:h-56 lg:h-64 object-cover rounded shadow cursor-pointer" @click="showModal = true">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Property Details (Bottom on mobile, right on large screens) -->
                    <div class="@if($property->image) lg:col-span-2 order-2 lg:order-2 @else lg:col-span-3 @endif">
                        <div class="space-y-3 md:space-y-4">
                            <div>
                                <span class="font-bold text-lg md:text-xl lg:text-2xl">{{ $property->name ?? '' }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-sm md:text-base">Property address</span><br>
                                <span class="text-sm md:text-base lg:text-lg">{{ $property->address ?? '' }}</span>
                            </div>
                            @if($property->special_instructions)
                            <div>
                                <span class="font-bold text-sm md:text-base">Special instructions</span><br>
                                <span class="text-sm md:text-base lg:text-lg">{{ $property->special_instructions }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Property Image Modal -->
            @if($property->image)
                <div x-data="{ showModal: false }">
                    <template x-if="showModal">
                        <div class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50" @click.self="showModal = false">
                            <img src="{{ asset('storage/' . $property->image) }}" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white cursor-pointer" @click="showModal = false">
                        </div>
                    </template>
                </div>
            @endif
        </div>
        <div class="mb-4">
            <div class="font-bold text-lg mb-2 text-gray-800">Requester Info</div>
            <div class="mb-1"><span class="font-semibold text-gray-600">Requester name</span><div class="font-medium text-gray-900 text-base mb-2">{{ $requester['name'] ?? '' }}</div></div>
            <div class="mb-1"><span class="font-semibold text-gray-600">Email</span><div class="font-medium text-gray-900 text-base mb-2">{{ $requester['email'] ?? '' }}</div></div>
            <div class="mb-1"><span class="font-semibold text-gray-600">Phone</span><div class="font-medium text-gray-900 text-base mb-2">{{ $requester['phone'] ?? '' }}</div></div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4">
            <div class="font-bold text-lg mb-2">Comments</div>
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
        <form method="POST" action="{{ route('mobile.request.comment', $request->id) }}" class="mb-4" enctype="multipart/form-data" id="technician-comment-form">
            @csrf
            <textarea name="comment" class="w-full border rounded p-2 mb-2" placeholder="Add a comment..." required></textarea>
            
            <!-- Media Upload Section -->
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">📎 Attach Media (Optional)</label>
                <input type="file" name="media[]" id="technician-comment-media" class="w-full border rounded p-2 text-sm" 
                       multiple accept="image/*,video/*" onchange="previewTechnicianMedia(this)">
                <div class="text-xs text-gray-500 mt-1">
                    Images: JPG, PNG, GIF (max 10MB) | Videos: MP4, MOV, AVI (max 50MB)
                </div>
                <div id="technician-media-preview" class="mt-2 grid grid-cols-2 gap-2 hidden"></div>
            </div>
            
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Comment</button>
        </form>
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
// Media preview functionality for technician
function previewTechnicianMedia(input) {
    const preview = document.getElementById('technician-media-preview');
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

// Checklist checkbox functionality
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
             .then(response => {
                 console.log('Response status:', response.status);
                 console.log('Response headers:', response.headers);
                 
                 if (!response.ok) {
                     throw new Error(`HTTP error! status: ${response.status}`);
                 }
                 
                 return response.json();
             })
                         .then(data => {
                 if (data.success) {
                     // Show success feedback
                     const feedback = document.createElement('div');
                     feedback.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                     feedback.textContent = isChecked ? 'Item completed!' : 'Item unchecked';
                     document.body.appendChild(feedback);
                     
                     setTimeout(() => {
                         feedback.remove();
                         // Refresh the page to ensure synchronization
                         window.location.reload();
                     }, 1000);
                 } else {
                     // Revert checkbox state on error
                     this.checked = !isChecked;
                     if (isChecked) {
                         label.classList.remove('line-through', 'text-gray-500');
                     } else {
                         label.classList.add('line-through', 'text-gray-500');
                     }
                     
                     // Show error feedback with specific message
                     const feedback = document.createElement('div');
                     feedback.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
                     feedback.textContent = data.message || 'Error updating item';
                     document.body.appendChild(feedback);
                     
                     setTimeout(() => {
                         feedback.remove();
                     }, 3000);
                 }
             })
                         .catch(error => {
                 console.error('Error details:', error);
                 console.error('Error message:', error.message);
                 console.error('Error stack:', error.stack);
                 
                 // Revert checkbox state on error
                 this.checked = !isChecked;
                 if (isChecked) {
                     label.classList.remove('line-through', 'text-gray-500');
                 } else {
                     label.classList.add('line-through', 'text-gray-500');
                 }
                 
                 // Show detailed error feedback
                 const feedback = document.createElement('div');
                 feedback.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
                 feedback.textContent = `Error: ${error.message}`;
                 document.body.appendChild(feedback);
                 
                 setTimeout(() => {
                     feedback.remove();
                 }, 5000);
             });
        });
    });
});
</script>
@endsection 