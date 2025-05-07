@extends('layouts.mobile')

@section('title', 'Property Details')

@php
$pageTitle = 'Property Details - Manager';
$pageUrl = 'maintainxtra.com/m/p/' . $property->id;
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
<div class="section-title">Property Details</div>

<div class="property-details">
    <div style="display: flex; margin-bottom: 20px;">
        <div style="width: 100px; height: 100px; background: #eee; margin-right: 15px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            @if($property->image)
                <img src="{{ asset('storage/' . $property->image) }}" alt="{{ $property->name }}" style="max-width: 100%; max-height: 100%; object-fit: cover;">
            @else
                <i class="fas fa-building" style="font-size: 36px; color: #999;"></i>
            @endif
        </div>
        <div>
            <h2 style="font-size: 20px; margin: 0 0 5px 0;">{{ $property->name }}</h2>
            <p style="margin: 0 0 5px 0; color: #666;">{{ $property->address }}</p>
            <div style="font-size: 14px; color: #0000ff;">
                <a href="{{ route('mobile.properties.edit', $property) }}">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>
    
    @if($property->special_instructions)
        <div style="margin-bottom: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px;">Special Instructions:</div>
            <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
                {{ $property->special_instructions }}
            </div>
        </div>
    @endif
    
    <div style="display: flex; justify-content: space-around; margin: 20px 0;">
        <a href="#" style="text-align: center; color: #333; text-decoration: none;">
            <div style="font-size: 24px; margin-bottom: 5px;"><i class="fas fa-qrcode"></i></div>
            <div>QR Code</div>
        </a>
        <a href="#" style="text-align: center; color: #333; text-decoration: none;">
            <div style="font-size: 24px; margin-bottom: 5px;"><i class="fas fa-link"></i></div>
            <div>Copy Link</div>
        </a>
    </div>
</div>

<div class="section-subtitle" style="padding: 0 15px;">Recent Maintenance Requests</div>

@if(count($maintenanceRequests) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Issue</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenanceRequests as $request)
                <tr>
                    <td>{{ Str::limit($request->description, 30) }}</td>
                    <td>
                        <span class="status-tag">{{ ucfirst($request->status) }}</span>
                    </td>
                    <td>{{ $request->created_at->format('d M, Y') }}</td>
                    <td class="view-icon">
                        <a href="{{ route('mobile.maintenance.index', ['id' => $request->id]) }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center; padding: 20px;">
        No maintenance requests for this property
    </div>
@endif

<div style="margin: 20px 15px;">
    <a href="{{ route('mobile.properties.index') }}" style="color: #0000ff; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to All Properties
    </a>
</div>
@endsection 