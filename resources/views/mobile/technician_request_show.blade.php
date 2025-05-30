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
            <div class="text-xs text-gray-500 mb-2">{{ $request->created_at ? date('d M, Y', strtotime($request->created_at)) : '' }}<br>{{ $request->created_at ? date('H:i', strtotime($request->created_at)) : '' }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="text-center {{ strtolower($request->priority) == 'high' ? 'bg-red-500 text-white' : (strtolower($request->priority) == 'low' ? 'bg-yellow-200' : (strtolower($request->priority) == 'medium' ? 'bg-yellow-100' : 'bg-gray-100')) }} font-bold py-3 rounded text-lg">
                {{ ucfirst($request->priority) }}
            </div>
            <div class="text-xs text-left">
                <div><span class="font-semibold">Started:</span> {{ $request->started_at ? date('d M, Y H:i', strtotime($request->started_at)) : '' }}</div>
                <div><span class="font-semibold">Finished:</span> {{ $request->completed_at ? date('d M, Y H:i', strtotime($request->completed_at)) : '' }}</div>
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
            <div class="font-semibold">Request title</div>
            <div class="border rounded p-2 text-sm bg-gray-50">{{ $request->title ?? '' }}</div>
        </div>
        <div class="mb-4">
            <div class="font-semibold">Description</div>
            <div class="border rounded p-2 text-sm bg-gray-50">{{ $request->description ?? '' }}</div>
        </div>
        <div class="mb-4">
            <div class="font-semibold">Location</div>
            <div class="border rounded p-2 text-sm bg-gray-50">{{ $request->location ?? '' }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4">
            <div class="font-semibold">Images</div>
            <div class="flex gap-2 flex-wrap">
                @foreach($request->images as $img)
                    <a href="{{ asset('storage/' . $img->path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $img->path) }}" class="w-24 h-16 object-cover rounded border" alt="Request Image">
                    </a>
                @endforeach
            </div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4">
            <div class="font-bold text-lg mb-2">Property Details</div>
            <div class="mb-1"><span class="font-semibold">Property name</span><div class="border rounded p-2 text-sm bg-gray-50">{{ $property->name ?? '' }}</div></div>
            <div class="mb-1"><span class="font-semibold">Property address</span><div class="border rounded p-2 text-sm bg-gray-50">{{ $property->address ?? '' }}</div></div>
            <div class="mb-1"><span class="font-semibold">Special instructions</span><div class="border rounded p-2 text-sm bg-gray-50">{{ $property->special_instructions ?? '' }}</div></div>
            <div class="mb-1"><span class="font-semibold">Property image</span>
                @if(!empty($property->image))
                    <img src="{{ asset('storage/' . $property->image) }}" class="w-full h-32 object-cover rounded border" alt="Property Image">
                @else
                    <div class="border rounded p-2 text-xs text-gray-400">No image</div>
                @endif
            </div>
        </div>
        <div class="mb-4">
            <div class="font-bold text-lg mb-2">Requester Info</div>
            <div class="mb-1"><span class="font-semibold">Requester name</span><div class="border rounded p-2 text-sm bg-gray-50">{{ $requester['name'] ?? '' }}</div></div>
            <div class="mb-1"><span class="font-semibold">Email</span><div class="border rounded p-2 text-sm bg-gray-50">{{ $requester['email'] ?? '' }}</div></div>
            <div class="mb-1"><span class="font-semibold">Phone</span><div class="border rounded p-2 text-sm bg-gray-50">{{ $requester['phone'] ?? '' }}</div></div>
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
                            <div class="text-sm">{{ $comment->comment }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <form method="POST" action="{{ route('mobile.request.comment', $request->id) }}" class="mb-4">
            @csrf
            <textarea name="comment" class="w-full border rounded p-2 mb-2" placeholder="Add a comment..." required></textarea>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Comment</button>
        </form>
    </div>
</div>
@endsection 