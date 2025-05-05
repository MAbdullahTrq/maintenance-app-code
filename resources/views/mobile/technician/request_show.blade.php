@extends('mobile.layout')

@section('title', 'Request – Technician')

@section('content')
<div style="background:#ffff99;padding:4px 0;text-align:center;font-weight:bold;">{{ ucfirst($request->status) }} Maintenance Request</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">
    maintainxtra.com/t/r/{{ $request->status }}
</div>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
    <span style="font-weight:bold;font-size:1.5em;color:#222;"><span style="color:#00f;">Maintain</span>Xtra</span>
    <span style="font-size:1em;">Technician &gt;</span>
</div>
<div style="background:#222;color:#fff;padding:8px 0;margin-bottom:8px;text-align:center;">
    <div style="font-size:1.2em;font-weight:bold;">{{ $request->property_name }}</div>
    <div>{{ $request->date }}</div>
</div>
<div style="display:flex;align-items:center;margin-bottom:8px;">
    <div style="flex:1;background:{{ $request->priority == 'High' ? '#ff4444' : '#ffff88' }};color:#222;padding:8px;text-align:center;">
        {{ $request->priority }}
    </div>
    <div style="flex:2;padding:8px;">
        <div>Started: {{ $request->started_at ? $request->started_at->format('d M, Y H:i') : '-' }}</div>
        <div>Finished: {{ $request->finished_at ? $request->finished_at->format('d M, Y H:i') : '-' }}</div>
    </div>
</div>

@if($request->status == 'assigned')
    <form action="{{ route('mobile.technician.request.action', [$request->id, 'accept']) }}" method="POST" style="margin-bottom:8px;">
        @csrf
        <div style="display:flex;gap:8px;">
            <button type="submit" name="action" value="decline" style="flex:1;padding:8px;background:#ccc;">Decline</button>
            <button type="submit" name="action" value="accept" style="flex:1;padding:8px;background:#4caf50;color:#fff;">Accept</button>
        </div>
    </form>
@endif

@if($request->status == 'accepted')
    <form action="{{ route('mobile.technician.request.action', [$request->id, 'start']) }}" method="POST" style="margin-bottom:8px;">
        @csrf
        <button type="submit" style="width:100%;padding:8px;background:#4caf50;color:#fff;">Start</button>
    </form>
@endif

@if($request->status == 'started')
    <form action="{{ route('mobile.technician.request.action', [$request->id, 'finish']) }}" method="POST" style="margin-bottom:8px;">
        @csrf
        <button type="submit" style="width:100%;padding:8px;background:#ffeb3b;color:#222;">Finished</button>
    </form>
@endif

<div style="margin-bottom:8px;">
    <label>Request title</label>
    <input type="text" value="{{ $request->title }}" readonly style="width:100%;padding:4px;">
</div>
<div style="margin-bottom:8px;">
    <label>Description</label>
    <input type="text" value="{{ $request->description }}" readonly style="width:100%;padding:4px;">
</div>
<div style="margin-bottom:8px;">
    <label>Location</label>
    <input type="text" value="{{ $request->location }}" readonly style="width:100%;padding:4px;">
</div>
<div style="margin-bottom:8px;">
    <label>Property image</label>
    <img src="/icons/house.png" alt="Property image" style="width:100%;max-width:220px;">
</div>
<div style="margin-bottom:8px;">
    <label>Images</label>
    <div>
        @foreach($request->images as $img)
            <img src="{{ $img->url }}" alt="Request image" style="width:100%;max-width:220px;margin-bottom:4px;" onclick="window.open('{{ $img->url }}','_blank')">
        @endforeach
    </div>
    <div style="font-size:0.85em;color:#888;">(Tap image to open full screen, scroll if more than one)</div>
</div>
@endsection 