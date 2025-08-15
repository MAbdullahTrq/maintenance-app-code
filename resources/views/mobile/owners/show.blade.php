@extends('mobile.layout')

@section('title', 'View Owner - Manager')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div class="mb-2 flex items-center">
            <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        
        <h2 class="text-center text-xl md:text-2xl lg:text-3xl font-bold mb-2 md:mb-4 text-yellow-600">{{ $owner->displayName }}</h2>
        
        <!-- Owner Details -->
        <div class="mb-4 md:mb-6">
            <div class="font-bold text-lg md:text-xl lg:text-2xl mb-2 md:mb-3">Owner Details</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="mb-2"><span class="font-semibold text-sm md:text-base">Name:</span> <span class="text-sm md:text-base lg:text-lg">{{ $owner->name }}</span></div>
                    <div class="mb-2"><span class="font-semibold text-sm md:text-base">Email:</span> <span class="text-sm md:text-base lg:text-lg">{{ $owner->email }}</span></div>
                    <div class="mb-1"><span class="font-semibold text-sm md:text-base">Phone:</span> <span class="text-sm md:text-base lg:text-lg">{{ $owner->phone ?? '-' }}</span></div>
                </div>
                <div>
                    <div class="mb-2"><span class="font-semibold text-sm md:text-base">Company:</span> <span class="text-sm md:text-base lg:text-lg">{{ $owner->company ?? '-' }}</span></div>
                    @if($owner->address)
                    <div class="mb-2"><span class="font-semibold text-sm md:text-base">Address:</span> <span class="text-sm md:text-base lg:text-lg">{{ $owner->address }}</span></div>
                    @endif
                    <div class="mb-1"><span class="font-semibold text-sm md:text-base">Properties:</span> <span class="text-sm md:text-base lg:text-lg">{{ $owner->properties->count() }}</span></div>
                </div>
            </div>
            @if($owner->notes)
            <div class="mt-4">
                <div class="font-semibold text-sm md:text-base mb-1">Notes:</div>
                <div class="text-sm md:text-base lg:text-lg bg-gray-50 p-3 rounded">{{ $owner->notes }}</div>
            </div>
            @endif
        </div>
        
        @if(!Auth::user()->isViewer())
        <div class="flex gap-2 md:gap-3 lg:gap-4 mb-4">
            <a href="/m/ao/{{ $owner->id }}/edit" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-blue-200 transition text-center text-sm md:text-base">Edit Owner</a>
                         <a href="{{ $owner->getOwnerUrl() }}" target="_blank" class="flex-1 bg-green-100 text-green-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-green-200 transition text-center text-sm md:text-base">Public Link</a>
            <a href="{{ route('mobile.owners.qrcode', $owner->id) }}" class="flex-1 bg-purple-100 text-purple-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-purple-200 transition text-center text-sm md:text-base">QR Code</a>
            <form action="{{ route('mobile.owners.destroy', $owner->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this owner?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-100 text-red-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-red-200 transition text-center text-sm md:text-base">Delete Owner</button>
            </form>
        </div>
        @endif
        
        <hr class="my-4 border-gray-300">
        
        <!-- Properties List -->
        @if($properties->count() > 0)
        <div class="font-bold text-lg md:text-xl lg:text-2xl mb-2 md:mb-3">Properties ({{ $properties->count() }})</div>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Address</th>
                        <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($properties as $property)
                    <tr class="border-b border-gray-400 hover:bg-gray-50">
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 font-semibold">{{ $property->name }}</td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                            <div class="text-gray-700 text-xs md:text-sm">
                                <span class="md:hidden">{{ Str::limit($property->address, 15) }}</span>
                                <span class="hidden md:inline">{{ Str::limit($property->address, 30) }}</span>
                            </div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                            <a href="{{ route('mobile.properties.show', $property->id) }}" class="text-blue-600 hover:text-blue-800 text-lg md:text-xl">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($properties->hasPages())
            <div class="mt-4">
                {{ $properties->links() }}
            </div>
        @endif
        @else
        <div class="text-center py-8">
            <div class="text-gray-500 mb-4">No properties assigned to this owner yet.</div>
            @if(!Auth::user()->isViewer())
            <a href="{{ route('mobile.properties.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add Property for {{ $owner->displayName }}
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection 