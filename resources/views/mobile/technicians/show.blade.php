@extends('layouts.mobile')

@section('title', 'Technician Details')

@php
$pageTitle = 'Technician Details - Manager';
$pageUrl = 'maintainxtra.com/m/t/' . $user->id;
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
<div class="section-title">Technician Details</div>

<div class="property-details">
    <div style="display: flex; margin-bottom: 20px;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: #eee; margin-right: 15px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-user" style="font-size: 36px; color: #999;"></i>
        </div>
        <div>
            <h2 style="font-size: 20px; margin: 0 0 5px 0;">{{ $user->name }}</h2>
            <p style="margin: 0 0 5px 0; color: #666;">{{ $user->email }}</p>
            <div style="font-size: 14px; color: #0000ff;">
                <a href="{{ route('mobile.technicians.edit', $user) }}">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>
    
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Status:</div>
        <div style="background: {{ $user->active ? '#e8f5e9' : '#ffebee' }}; padding: 10px; border-radius: 4px; color: {{ $user->active ? 'green' : 'red' }};">
            {{ $user->active ? 'Active' : 'Inactive' }}
        </div>
    </div>
    
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Contact Information:</div>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
            <div><strong>Email:</strong> {{ $user->email }}</div>
            <div><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</div>
        </div>
    </div>
    
    @if(isset($user->notes))
    <div style="margin-bottom: 15px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Notes:</div>
        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px;">
            {{ $user->notes }}
        </div>
    </div>
    @endif
</div>

<div class="section-subtitle" style="padding: 0 15px;">Current Assignments</div>

@if(count($assignedRequests) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Property</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignedRequests as $request)
                <tr>
                    <td>
                        {{ $request->property->name }}<br>
                        <small>{{ Str::limit($request->description, 20) }}</small>
                    </td>
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
        No current assignments
    </div>
@endif

<div style="margin: 20px 15px;">
    <a href="{{ route('mobile.technicians.index') }}" style="color: #0000ff; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to All Technicians
    </a>
</div>
@endsection 