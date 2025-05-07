@extends('layouts.mobile')

@section('title', 'Maintenance Request')

@php
$pageTitle = 'Request Details - Manager';
$pageUrl = 'maintainxtra.com/m/r/' . $request->id;
@endphp

@section('nav-icons')
<div class="nav-icons">
    <div class="nav-icon">
        <a href="{{ route('mobile.manager.dashboard') }}" style="color: inherit; text-decoration: none;">
            <i class="fas fa-home"></i>
            <div class="count">0</div>
            <div class="add">+</div>
        </a>
    </div>
    <div class="nav-icon">
        <a href="{{ route('mobile.technicians.index') }}" style="color: inherit; text-decoration: none;">
            <i class="fas fa-hard-hat"></i>
            <div class="count">1</div>
            <div class="add">+</div>
        </a>
    </div>
    <div class="nav-icon">
        <a href="{{ route('mobile.maintenance.index') }}" style="color: inherit; text-decoration: none;">
            <i class="fas fa-clipboard-list"></i>
            <div class="count">2</div>
            <div class="add">+</div>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="section-title">Request Details</div>

<div class="property-details">
    <div style="background: {{ $request->priority == 'high' ? '#ffebee' : '#fff9c4' }}; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-weight: bold; color: {{ $request->priority == 'high' ? 'red' : 'black' }};">
        {{ ucfirst($request->priority) }} Priority
    </div>
    
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Property:</div>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
            {{ $request->property->name }}<br>
            {{ $request->property->address }}
        </div>
    </div>
    
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Description:</div>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
            {{ $request->description }}
        </div>
    </div>
    
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Status:</div>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; font-weight: bold;">
            {{ ucfirst($request->status) }}
        </div>
    </div>
    
    @if($request->technician)
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Assigned Technician:</div>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
            {{ $request->technician->name }}<br>
            <small>{{ $request->technician->email }}</small>
        </div>
    </div>
    @endif
    
    @if($request->images && count($request->images) > 0)
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Request Images:</div>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @foreach($request->images as $image)
                <div style="width: calc(50% - 5px); height: 120px; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <img src="{{ asset('storage/' . $image->path) }}" alt="Request image" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    @if($request->status === 'pending')
    <div class="action-buttons">
        <a href="#" class="btn btn-decline" onclick="event.preventDefault(); document.getElementById('decline-form').submit();">Decline</a>
        <a href="#" class="btn btn-approve" onclick="event.preventDefault(); document.getElementById('approve-form').submit();">Approve</a>
    </div>
    
    <form id="approve-form" action="{{ route('mobile.maintenance.approve', $request) }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <form id="decline-form" action="{{ route('mobile.maintenance.decline', $request) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
    
    @if($request->status === 'completed')
    <div style="margin-top: 15px;">
        <button class="btn-complete" onclick="event.preventDefault(); document.getElementById('complete-form').submit();">
            Mark as Closed
        </button>
    </div>
    
    <form id="complete-form" action="{{ route('mobile.maintenance.complete', $request) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
</div>

@if(count($request->comments) > 0)
<div class="section-subtitle" style="padding: 0 15px;">Activity</div>

<div style="margin: 0 15px;">
    @foreach($request->comments as $comment)
    <div style="background: white; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <div style="font-weight: bold;">{{ $comment->user->name }}</div>
            <div style="color: #666; font-size: 12px;">{{ $comment->created_at->format('d M, Y H:i') }}</div>
        </div>
        <div>{{ $comment->comment }}</div>
        
        @if($comment->has_image)
        <div style="margin-top: 10px; height: 120px; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img src="{{ asset('storage/' . $comment->image_path) }}" alt="Comment image" style="max-width: 100%; max-height: 100%; object-fit: cover;">
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

<div style="margin: 20px 15px;">
    <a href="{{ route('mobile.maintenance.index') }}" style="color: #0000ff; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to All Requests
    </a>
</div>
@endsection 