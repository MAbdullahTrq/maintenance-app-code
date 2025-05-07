@extends('layouts.mobile')

@section('title', 'Maintenance Task')

@php
$pageTitle = 'Task Details - Technician';
$pageUrl = 'maintainxtra.com/m/t/r/' . $request->id;
@endphp

@section('nav-icons')
<div class="nav-icons">
    <div class="nav-icon">
        <a href="{{ route('mobile.technician.assigned') }}" style="color: inherit; text-decoration: none;">
            <div>Assigned</div>
            <div class="count">{{ $assignedCount ?? 0 }}</div>
        </a>
    </div>
    <div class="nav-icon">
        <a href="{{ route('mobile.technician.accepted') }}" style="color: inherit; text-decoration: none;">
            <div>Accepted</div>
            <div class="count">{{ $acceptedCount ?? 0 }}</div>
        </a>
    </div>
    <div class="nav-icon">
        <a href="{{ route('mobile.technician.completed') }}" style="color: inherit; text-decoration: none;">
            <div>Completed</div>
            <div class="count">{{ $completedCount ?? 0 }}</div>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="section-title">Task Details</div>

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
    
    @if($request->special_instructions)
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Special Instructions:</div>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
            {{ $request->special_instructions }}
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
    
    @if($request->status === 'assigned')
    <div class="action-buttons">
        <a href="#" class="btn btn-decline" onclick="event.preventDefault(); document.getElementById('decline-form').submit();">Decline</a>
        <a href="#" class="btn btn-approve" onclick="event.preventDefault(); document.getElementById('accept-form').submit();">Accept</a>
    </div>
    
    <form id="accept-form" action="{{ route('mobile.technician.accept', $request) }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <form id="decline-form" action="{{ route('mobile.technician.decline', $request) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
    
    @if($request->status === 'acknowledged')
    <a href="#" class="btn-start" onclick="event.preventDefault(); document.getElementById('start-form').submit();">
        Start Task
    </a>
    
    <form id="start-form" action="{{ route('mobile.technician.start', $request) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
    
    @if($request->status === 'started')
    <a href="#" class="btn-finish" onclick="event.preventDefault(); document.getElementById('finish-modal').style.display = 'block';">
        Finish Task
    </a>
    
    <div id="finish-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 500px; background: white; padding: 20px; border-radius: 4px;">
            <h2 style="margin-top: 0;">Complete Task</h2>
            
            <form action="{{ route('mobile.technician.finish', $request) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="comment">Completion Notes</label>
                    <textarea id="comment" name="comment" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="images">Completion Images</label>
                    <input type="file" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/gif">
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        Please upload images showing the completed work.
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                    <button type="button" onclick="document.getElementById('finish-modal').style.display = 'none';" style="padding: 10px 15px; border: 1px solid #ddd; background: white;">
                        Cancel
                    </button>
                    <button type="submit" style="padding: 10px 15px; background: black; color: white; border: none;">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
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
    <a href="{{ route('mobile.technician.dashboard') }}" style="color: #0000ff; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>
@endsection 