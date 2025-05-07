@extends('layouts.mobile')

@section('title', 'Edit Technician')

@php
$pageTitle = 'Edit Technician - Manager';
$pageUrl = 'maintainxtra.com/m/et/' . $user->id;
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
<div class="section-title">Edit Technician</div>

<form action="{{ route('mobile.technicians.update', $user) }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label for="name">Name*</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email*</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>
    
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
    </div>
    
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3">{{ old('notes', $user->notes) }}</textarea>
    </div>
    
    <div class="form-group">
        <label for="active">Status</label>
        <select id="active" name="active">
            <option value="1" {{ $user->active ? 'selected' : '' }}>Active</option>
            <option value="0" {{ !$user->active ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    
    <div class="form-actions">
        <a href="{{ route('mobile.technicians.index') }}" class="btn btn-cancel">Cancel</a>
        <button type="submit" class="btn btn-save">Save</button>
    </div>
</form>

<form action="{{ route('mobile.technicians.reset-password', $user) }}" method="POST" style="margin-top: 30px;">
    @csrf
    <button type="submit" class="btn-complete" onclick="return confirm('Are you sure you want to reset password for this technician?')">
        Reset Password
    </button>
</form>

<form action="{{ route('mobile.technicians.destroy', $user) }}" method="POST" style="margin-top: 15px;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this technician?')">
        Delete Technician
    </button>
</form>
@endsection 