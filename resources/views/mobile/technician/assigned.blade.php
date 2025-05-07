@extends('layouts.mobile')

@section('title', 'Assigned Tasks')

@php
$pageTitle = 'Assigned Tasks - Technician';
$pageUrl = 'maintainxtra.com/m/t/r/assigned';
@endphp

@section('nav-icons')
<div class="nav-icons">
    <div class="nav-icon">
        <a href="{{ route('mobile.technician.assigned') }}" style="color: inherit; text-decoration: none;">
            <div>Assigned</div>
            <div class="count">{{ $requests->count() }}</div>
        </a>
    </div>
    <div class="nav-icon">
        <a href="{{ route('mobile.technician.accepted') }}" style="color: inherit; text-decoration: none;">
            <div>Accepted</div>
            <div class="count">0</div>
        </a>
    </div>
    <div class="nav-icon">
        <a href="{{ route('mobile.technician.completed') }}" style="color: inherit; text-decoration: none;">
            <div>Completed</div>
            <div class="count">0</div>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="section-title">Assigned Tasks</div>

<div class="search-bar">
    <input type="text" placeholder="Search">
</div>

@if(count($requests) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Property</th>
                <th>Priority <i class="fas fa-chevron-right"></i></th>
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
                        {{ ucfirst($request->priority) }}
                    </td>
                    <td>
                        {{ $request->created_at->format('d M, Y') }}<br>
                        <small>{{ $request->created_at->format('H:i') }}</small>
                    </td>
                    <td class="view-icon">
                        <a href="{{ route('mobile.technician.assigned', ['id' => $request->id]) }}">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center; padding: 20px;">
        You have no assigned tasks
    </div>
@endif

<div style="margin: 20px 15px;">
    <a href="{{ route('mobile.technician.dashboard') }}" style="color: #0000ff; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>
@endsection 