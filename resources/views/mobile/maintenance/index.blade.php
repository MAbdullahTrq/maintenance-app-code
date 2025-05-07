@extends('layouts.mobile')

@section('title', 'All Maintenance Requests')

@php
$pageTitle = 'All Requests - Manager';
$pageUrl = 'maintainxtra.com/m/ar/';
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
<div class="section-title">Maintenance Requests</div>

<div style="display: flex; margin: 15px; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
    <a href="{{ route('mobile.maintenance.pending') }}" style="min-width: 80px; text-align: center; padding: 8px 12px; background: #f0f0f0; border-radius: 20px; text-decoration: none; color: #333; white-space: nowrap;">
        Pending ({{ $pendingCount }})
    </a>
    <a href="{{ route('mobile.maintenance.assigned') }}" style="min-width: 80px; text-align: center; padding: 8px 12px; background: #f0f0f0; border-radius: 20px; text-decoration: none; color: #333; white-space: nowrap;">
        Assigned ({{ $assignedCount }})
    </a>
    <a href="{{ route('mobile.maintenance.completed') }}" style="min-width: 80px; text-align: center; padding: 8px 12px; background: #f0f0f0; border-radius: 20px; text-decoration: none; color: #333; white-space: nowrap;">
        Completed ({{ $completedCount }})
    </a>
</div>

<div class="search-bar">
    <input type="text" placeholder="Search requests">
</div>

@if(count($requests) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Property</th>
                <th>Status <i class="fas fa-chevron-right"></i></th>
                <th>Date <i class="fas fa-chevron-right"></i></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>
                        {{ $request->property->name }}<br>
                        <small>{{ Str::limit($request->description, 25) }}</small>
                    </td>
                    <td class="{{ $request->priority == 'high' ? 'priority-high' : 'priority-low' }}">
                        {{ ucfirst($request->status) }}
                    </td>
                    <td>
                        {{ $request->created_at->format('d M, Y') }}<br>
                        <small>{{ $request->created_at->format('H:i') }}</small>
                    </td>
                    <td class="view-icon">
                        <a href="{{ route('mobile.maintenance.pending', ['id' => $request->id]) }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center; padding: 20px;">
        No maintenance requests found
    </div>
@endif
@endsection 