@extends('mobile.layout')

@section('title', ucfirst($request->status) . ' Request – Technician')

@section('header-actions')
<a href="#" class="text-sm font-medium">Technician &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="mb-2">
            <a href="{{ route('mobile.technician.dashboard') }}" class="text-blue-700 text-sm hover:underline flex items-center mb-2"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        <div class="text-center mb-2">
            <span class="inline-block bg-gray-200 px-3 py-1 rounded text-xs font-semibold mb-2">{{ ucfirst($request->status) }}</span>
            <div class="font-bold text-xl">Maintenance Request</div>
            <div class="text-sm text-gray-700">({{ $request->property->name ?? '' }})</div>
            <div class="text-xs text-gray-500 mb-2">{{ $request->created_at ? date('d M, Y', strtotime($request->created_at)) : '' }}<br>{{ $request->created_at ? date('H:i', strtotime($request->created_at)) : '' }}</div>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-2">
            <div class="text-center {{ strtolower($request->priority) == 'high' ? 'bg-red-500 text-white' : (strtolower($request->priority) == 'low' ? 'bg-yellow-200' : (strtolower($request->priority) == 'medium' ? 'bg-yellow-100' : 'bg-gray-100')) }} font-bold py-2 rounded">
                {{ ucfirst($request->priority) }}
            </div>
            <div class="text-xs text-left">
                <div><span class="font-semibold">Started:</span> {{ $request->started_at ? date('d M, Y H:i', strtotime($request->started_at)) : '' }}</div>
                <div><span class="font-semibold">Finished:</span> {{ $request->completed_at ? date('d M, Y H:i', strtotime($request->completed_at)) : '' }}</div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-4">
            @if($request->status === 'assigned')
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="w-full bg-gray-300 text-black py-2 rounded font-semibold">Decline</button>
                </form>
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded font-semibold">Accept</button>
                </form>
            @elseif($request->status === 'accepted')
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-semibold">Start</button>
                </form>
            @elseif($request->status === 'started')
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="w-full bg-yellow-500 text-black py-2 rounded font-semibold">Finish</button>
                </form>
            @endif
        </div>
        <div class="mb-2">
            <div class="font-semibold">Request title</div>
            <div class="border rounded p-2 text-sm bg-gray-50">{{ $request->title ?? '' }}</div>
        </div>
        <div class="mb-2">
            <div class="font-semibold">Description</div>
            <div class="border rounded p-2 text-sm bg-gray-50">{{ $request->description ?? '' }}</div>
        </div>
        <div class="mb-2">
            <div class="font-semibold">Location</div>
            <div class="border rounded p-2 text-sm bg-gray-50">{{ $request->location ?? '' }}</div>
        </div>
        <div class="mb-2">
            <div class="font-semibold">Images</div>
            <div class="flex gap-2 flex-wrap">
                @foreach($request->images as $img)
                    <a href="{{ asset('storage/' . $img->path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $img->path) }}" class="w-24 h-16 object-cover rounded border" alt="Request Image">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection 