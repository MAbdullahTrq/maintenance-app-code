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
                        <div class="flex-shrink-0 mt-1">
                            @if($item->type === 'checkbox')
                                <div class="w-4 h-4 border-2 border-gray-300 rounded"></div>
                            @else
                                <div class="w-4 h-4 bg-gray-200 rounded"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $item->description }}
                                @if($item->is_required)
                                    <span class="text-red-500 ml-1">*</span>
                                @endif
                            </div>
                            @if($item->task_description)
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $item->task_description }}
                                </div>
                            @endif
                            <div class="text-xs text-gray-500 mt-1">
                                Type: {{ ucfirst($item->type) }}
                                @if($item->is_required)
                                    • Required
                                @endif
                            </div>
                            @if($item->attachment_path)
                                <div class="mt-2 flex justify-end">
                                    <button onclick="window.open('{{ $item->attachment_url }}', '_blank')" 
                                            class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-paperclip mr-1"></i>View Attachment
                                    </button>
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
@endsection 