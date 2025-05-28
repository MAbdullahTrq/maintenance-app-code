@extends('mobile.layout')

@section('title', 'View Property - Manager')

@section('content')
<div class="bg-white rounded-xl shadow p-4 max-w-md mx-auto">
    <h2 class="text-center text-2xl font-bold mb-2 text-yellow-600">{{ $property->name }}</h2>
    <div class="flex gap-2 mb-4">
        <a href="{{ route('mobile.properties.qrcode', $property->id) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 rounded-lg shadow hover:bg-blue-200 transition text-center">QR Code</a>
        <a href="{{ route('guest.request.form', $property->access_link) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 rounded-lg shadow hover:bg-blue-200 transition text-center">Link</a>
        <a href="{{ route('mobile.properties.edit', $property->id) }}" class="flex-1 bg-blue-100 text-blue-800 font-semibold py-2 rounded-lg shadow hover:bg-blue-200 transition text-center">Edit</a>
    </div>
    <div class="bg-white border rounded p-4 mb-6">
        <div class="mb-2">
            <span class="font-semibold">Property name</span><br>
            {{ $property->name }}
        </div>
        <div class="mb-2">
            <span class="font-semibold">Property address</span><br>
            <span class="font-bold">{{ $property->address }}</span>
        </div>
        @if($property->special_instructions)
        <div class="mb-2">
            <span class="font-semibold">Special instructions</span><br>
            <span class="font-bold">{{ $property->special_instructions }}</span>
        </div>
        @endif
        @if($property->image)
        <div class="mb-2">
            <span class="font-semibold">Property image</span><br>
            <img src="{{ asset('storage/' . $property->image) }}" alt="Property image" class="rounded mt-1 max-h-40">
        </div>
        @endif
    </div>
    <div>
        <h3 class="text-center text-lg font-bold mb-2">Maintenance requests</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-1 border-r border-gray-400">Title</th>
                        <th class="p-1 border-r border-gray-400">Date created &gt;</th>
                        <th class="p-1 border-r border-gray-400">Date completed &gt;</th>
                        <th class="p-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($property->maintenanceRequests as $req)
                    <tr class="border-b border-gray-400">
                        <td class="p-1 align-top border-r border-gray-400">{{ $req->title }}</td>
                        <td class="p-1 align-top border-r border-gray-400">{{ $req->created_at->format('d M, Y') }}<br><span class="text-xs text-gray-500">{{ $req->created_at->format('H:i') }}</span></td>
                        <td class="p-1 align-top border-r border-gray-400">@if($req->completed_at){{ $req->completed_at->format('d M, Y') }}<br><span class="text-xs text-gray-500">{{ $req->completed_at->format('H:i') }}</span>@endif</td>
                        <td class="p-1 align-top"><a href="/m/r/{{ $req->id }}"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<a href="{{ route('mobile.properties.create') }}" class="fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center text-3xl shadow-lg z-50">
    +
</a>
@endsection 