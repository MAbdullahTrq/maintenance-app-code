@extends('layouts.mobile')

@section('title', 'Manager Dashboard')

@php
$pageTitle = 'Dash – Manager';
$pageUrl = 'maintainxtra.com/m/dash';
@endphp

@section('nav-icons')
<div class="nav-icons">
    <div class="nav-icon">
        <i class="fas fa-home"></i>
        <div class="count">{{ $pendingRequests }}</div>
        <div class="add">+</div>
    </div>
    <div class="nav-icon">
        <i class="fas fa-hard-hat"></i>
        <div class="count">1</div>
        <div class="add">+</div>
    </div>
    <div class="nav-icon">
        <i class="fas fa-clipboard-list"></i>
        <div class="count">2</div>
        <div class="add">+</div>
    </div>
</div>
@endsection

@section('content')
<div class="section-title">Pending Requests</div>

@if(count($recentRequests) > 0)
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
            @foreach($recentRequests as $request)
                <tr>
                    <td>
                        {{ $request->property->name }}<br>
                        <small>{{ $request->property->address }}</small>
                    </td>
                    <td class="{{ $request->priority == 'high' ? 'priority-high' : 'priority-low' }}">
                        {{ ucfirst($request->priority) }}
                    </td>
                    <td>{{ $request->created_at->format('d M, Y') }}</td>
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
        You have no new Pending Requests
    </div>
@endif

<div style="margin-top: 20px; padding: 0 15px;">
    <a href="{{ route('mobile.properties.index') }}" style="display: block; margin-bottom: 10px; color: blue; text-decoration: none;">
        Link to All Properties pages
    </a>
    <a href="{{ route('mobile.technicians.index') }}" style="display: block; margin-bottom: 10px; color: blue; text-decoration: none;">
        Link to All Technicians page
    </a>
    <a href="{{ route('mobile.maintenance.index') }}" style="display: block; color: blue; text-decoration: none;">
        Link to All Requests page
    </a>
</div>
@endsection 