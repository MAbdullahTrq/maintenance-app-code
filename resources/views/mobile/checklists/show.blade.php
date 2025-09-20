@extends('mobile.layout')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">{{ $checklist->name }}</h1>
            <a href="{{ route('mobile.checklists.edit', $checklist->id) }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Edit
            </a>
        </div>
        
        @if($checklist->description)
            <p class="text-gray-600 mt-2">{{ $checklist->description }}</p>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Checklist Items ({{ $checklist->items->count() }})</h2>
        
        @if($checklist->items->count() > 0)
            <div class="space-y-3">
                @foreach($checklist->items as $item)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        @if($item->type !== 'header')
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded"></div>
                            </div>
                        @endif
                        <div class="flex-1">
                            @if($item->type === 'header')
                                <div class="text-xl font-bold text-gray-900">
                                    {{ $item->description }}
                                </div>
                            @else
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->description }}
                                    @if($item->is_required)
                                        <span class="text-red-500 ml-1">*</span>
                                    @endif
                                </div>
                            @endif
                            @if($item->task_description)
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $item->task_description }}
                                </div>
                            @endif
                            @if($item->is_required && $item->type !== 'header')
                                <div class="text-xs text-gray-500 mt-1">
                                    Required
                                </div>
                            @endif
                            @if($item->hasAttachments())
                                <div class="mt-2">
                                    <div class="flex space-x-1 justify-end">
                                        @foreach($item->getAllAttachmentPaths() as $attachmentPath)
                                            @php
                                                $isImage = in_array(strtolower(pathinfo($attachmentPath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $attachmentUrl = asset('storage/' . $attachmentPath);
                                            @endphp
                                            
                                            @if($isImage)
                                                <img src="{{ $attachmentUrl }}" 
                                                     alt="Attachment" 
                                                     class="w-8 h-8 object-cover rounded border border-gray-200 cursor-pointer hover:scale-110 transition-transform duration-200"
                                                     onclick="openImageModal('{{ $attachmentUrl }}')">
                                            @else
                                                <button onclick="window.open('{{ $attachmentUrl }}', '_blank')" 
                                                        class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                                    <i class="fas fa-paperclip mr-1"></i>View
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-sm">No items added yet</div>
                <a href="{{ route('mobile.checklists.edit', $checklist->id) }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                    Add your first item
                </a>
            </div>
        @endif
    </div>

    <div class="mt-6 flex space-x-3">
        <a href="{{ route('mobile.checklists.index') }}" 
           class="flex-1 bg-gray-500 text-white text-center py-3 rounded-lg font-medium">
            Back to Checklists
        </a>
        <a href="{{ route('mobile.checklists.edit', $checklist->id) }}" 
           class="flex-1 bg-blue-500 text-white text-center py-3 rounded-lg font-medium">
            Edit Checklist
        </a>
    </div>
</div>

<script>
function openImageModal(imageUrl) {
    // Create modal overlay
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    };
    
    // Create modal content
    const modalContent = document.createElement('div');
    modalContent.className = 'max-w-4xl max-h-full p-4';
    
    const img = document.createElement('img');
    img.src = imageUrl;
    img.className = 'max-w-full max-h-full object-contain rounded';
    img.onclick = function(e) {
        e.stopPropagation();
    };
    
    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.className = 'absolute top-4 right-4 text-white text-2xl hover:text-gray-300';
    closeBtn.onclick = function() {
        modal.remove();
    };
    
    modalContent.appendChild(img);
    modal.appendChild(modalContent);
    modal.appendChild(closeBtn);
    
    document.body.appendChild(modal);
}
</script>
@endsection 