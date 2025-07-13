@extends('mobile.layout')

@section('title', 'View Property - Manager')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
    <div class="mb-2 flex items-center">
        <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
    </div>
    <h2 class="text-center text-xl md:text-2xl lg:text-3xl font-bold mb-2 md:mb-4 text-yellow-600">{{ $property->name }}</h2>
    <div class="flex gap-2 md:gap-3 lg:gap-4 mb-4">
        <a href="{{ route('mobile.properties.qrcode', $property->id) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-blue-200 transition text-center text-sm md:text-base">QR Code</a>
        <a href="{{ route('guest.request.form', $property->access_link) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-blue-200 transition text-center text-sm md:text-base">Link</a>
        <a href="{{ route('mobile.properties.edit', $property->id) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 md:py-3 lg:py-4 rounded-lg shadow hover:bg-blue-200 transition text-center text-sm md:text-base">Edit</a>
    </div>
    <div class="bg-white border rounded p-3 md:p-4 lg:p-6 mb-4 md:mb-6">
        <div class="mb-3 md:mb-4">
            <span class="font-semibold text-sm md:text-base">Property name</span><br>
            <span class="text-sm md:text-base lg:text-lg">{{ $property->name }}</span>
        </div>
        <div class="mb-3 md:mb-4">
            <span class="font-semibold text-sm md:text-base">Property address</span><br>
            <span class="font-bold text-sm md:text-base lg:text-lg">{{ $property->address }}</span>
        </div>
        @if($property->special_instructions)
        <div class="mb-3 md:mb-4">
            <span class="font-semibold text-sm md:text-base">Special instructions</span><br>
            <span class="font-bold text-sm md:text-base lg:text-lg">{{ $property->special_instructions }}</span>
        </div>
        @endif
        @if($property->image)
        <div class="mb-2">
            <span class="font-semibold text-sm md:text-base">Property image</span><br>
            <div class="w-32 h-24 md:w-48 md:h-32 lg:w-64 lg:h-40 mx-auto mt-2">
                <img src="{{ asset('storage/' . $property->image) }}" alt="Property image" class="w-full h-full object-cover rounded shadow">
            </div>
        </div>
        @endif
    </div>
    <div>
        <h3 class="text-center text-lg md:text-xl lg:text-2xl font-bold mb-2 md:mb-4">Maintenance requests</h3>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Title</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Date created</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Date completed</th>
                        <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($property->maintenanceRequests as $req)
                    <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='/m/r/{{ $req->id }}'">
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 font-semibold">{{ $req->title }}</td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            <div>{{ $req->created_at->format('d M, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $req->created_at->format('H:i') }}</div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            @if($req->completed_at)
                                <div>{{ $req->completed_at->format('d M, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $req->completed_at->format('H:i') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                            <a href="/m/r/{{ $req->id }}" class="text-blue-600 hover:text-blue-800 text-lg md:text-xl" onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
@if(Auth::user()->hasActiveSubscription())
<a href="{{ route('mobile.properties.create') }}" class="fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center text-3xl shadow-lg z-50">
@else
    <a href="{{ route('mobile.subscription.plans') }}" class="fixed bottom-6 right-6 bg-gray-400 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg z-50" title="Subscription required">
        <i class="fas fa-lock text-xl"></i>
    </a>
@endif
    +
</a>
@endsection 