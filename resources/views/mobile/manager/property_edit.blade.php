@extends('mobile.layout')

@section('title', 'Edit Property – Manager')

@section('content')
<div style="background:#d4ffd4;padding:4px 0;text-align:center;font-weight:bold;">Edit Property - Manager</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">maintainxtra.com/m/ep/{{ $property->id }}</div>
@include('mobile.manager.partials.header')
<h2 style="text-align:center;font-size:1.2em;margin:16px 0 8px;">Edit Property</h2>
<form action="{{ route('mobile.manager.property.update', $property->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div style="margin-bottom:8px;">
        <label>Property name*</label><br>
        <input type="text" name="name" value="{{ $property->name }}" required style="width:100%;padding:4px;">
    </div>
    <div style="margin-bottom:8px;">
        <label>Property address*</label><br>
        <input type="text" name="address" value="{{ $property->address }}" required style="width:100%;padding:4px;">
    </div>
    <div style="margin-bottom:8px;">
        <label>Special instructions</label><br>
        <input type="text" name="instructions" value="{{ $property->instructions }}" style="width:100%;padding:4px;">
    </div>
    <div style="margin-bottom:8px;display:flex;align-items:center;">
        <div style="flex:1;">
            <label>Property image</label><br>
            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif">
            <div style="font-size:0.85em;color:#888;">(JPEG, PNG, JPG, GIF, max 2MB).</div>
        </div>
        <div style="flex:1;text-align:center;">
            @if($property->image_url)
            <img src="{{ $property->image_url }}" alt="Preview" style="max-width:80px;max-height:80px;">
            @endif
        </div>
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:16px;">
        <button type="button" onclick="window.history.back()" style="width:48%;padding:8px;">Cancel</button>
        <button type="submit" style="width:48%;padding:8px;background:#4caf50;color:#fff;">Save</button>
    </div>
</form>
@endsection 