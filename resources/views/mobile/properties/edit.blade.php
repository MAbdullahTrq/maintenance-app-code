@extends('layouts.mobile')

@section('title', 'Edit Property')

@php
$pageTitle = 'Edit Property - Manager';
$pageUrl = 'maintainxtra.com/m/ep/' . $property->id;
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
<div class="section-title">Edit Property</div>

<form action="{{ route('mobile.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label for="name">Property name*</label>
        <input type="text" id="name" name="name" value="{{ old('name', $property->name) }}" required>
    </div>
    
    <div class="form-group">
        <label for="address">Property address*</label>
        <input type="text" id="address" name="address" value="{{ old('address', $property->address) }}" required>
    </div>
    
    <div class="form-group">
        <label for="special_instructions">Special instructions</label>
        <textarea id="special_instructions" name="special_instructions" rows="3">{{ old('special_instructions', $property->special_instructions) }}</textarea>
    </div>
    
    <div class="form-group">
        <label for="image">Property image</label>
        <div style="display: flex; flex-direction: row; align-items: flex-start; gap: 15px;">
            <div style="flex: 1;">
                <input type="file" id="image" name="image" class="file-input" accept="image/jpeg,image/png,image/jpg,image/gif">
                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                    (JPEG, PNG, JPG, GIF, max 2MB).
                </div>
            </div>
            <div style="width: 100px; height: 100px; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                @if($property->image)
                    <img src="{{ asset('storage/' . $property->image) }}" alt="{{ $property->name }}" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                @else
                    <i class="fas fa-image" style="font-size: 24px; color: #999;"></i>
                @endif
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <a href="{{ route('mobile.properties.index') }}" class="btn btn-cancel">Cancel</a>
        <button type="submit" class="btn btn-save">Save</button>
    </div>
</form>

<div style="font-size: 12px; color: #666; margin-top: 20px; padding: 0 15px;">
    All images to be resized 400 x 600 or 600 x 400 and optimized on upload. No heavy files to slow server. Most people will be loading from phones so images high res so we need to do for them to make super user friendly.
</div>
@endsection 