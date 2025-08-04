@extends('mobile.layout')

@section('title', 'Checklists')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div x-data="{ search: '' }">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg md:text-xl lg:text-2xl">All Checklists</div>
                
                <!-- Add Checklist Button -->
                @if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
                <a href="/m/cl/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>Add
                </a>
                @elseif(Auth::user()->isViewer())
                    <!-- Viewers see no add button -->
                @else
                <a href="{{ route('mobile.subscription.plans') }}" class="bg-gray-400 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium" title="Subscription required">
                    <i class="fas fa-lock mr-1"></i>Add
                </a>
                @endif
            </div>
            
            <input type="text" x-model="search" placeholder="Search" class="w-full border rounded p-2 md:p-3 lg:p-4 mb-4 text-sm md:text-base" />
            
            <div class="space-y-3">
                @foreach($checklists as $checklist)
                <div x-show="search === '' || '{{ strtolower($checklist->name . ' ' . $checklist->description) }}'.includes(search.toLowerCase())" 
                     class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                     onclick="window.location.href='/m/cl/{{ $checklist->id }}'">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-clipboard-list text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm md:text-base">{{ $checklist->name }}</h3>
                                    <p class="text-gray-500 text-xs md:text-sm">{{ $checklist->items_count }} items</p>
                                </div>
                            </div>
                            <p class="text-gray-600 text-xs md:text-sm mb-2">
                                {{ Str::limit($checklist->description, 100) ?: 'No description' }}
                            </p>
                            <p class="text-gray-400 text-xs">Created {{ $checklist->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <a href="/m/cl/{{ $checklist->id }}" class="text-blue-600 hover:text-blue-800 p-2">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!Auth::user()->isViewer())
                            <a href="/m/cl/{{ $checklist->id }}/edit" class="text-green-600 hover:text-green-800 p-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('mobile.checklists.destroy', $checklist->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this checklist?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if($checklists->count() === 0)
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-clipboard-list text-4xl mb-4 text-gray-300"></i>
                    <p class="text-sm md:text-base">No checklists found.</p>
                    @if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
                    <a href="/m/cl/create" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Create Your First Checklist
                    </a>
                    @endif
                </div>
                @endif
            </div>
            
            <!-- Pagination -->
            @if($checklists->hasPages())
                <div class="mt-6">
                    {{ $checklists->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 