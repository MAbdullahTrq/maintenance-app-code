@extends('layouts.mobile')

@section('title', 'All Technicians')

@php
$pageTitle = 'All Technicians - Manager';
$pageUrl = 'maintainxtra.com/m/at/';
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
<div class="section-title">All Technicians</div>

<div class="search-bar">
    <input type="text" placeholder="Search">
</div>

@if(count($technicians) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($technicians as $technician)
                <tr>
                    <td style="width: 40px;">
                        <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #eee; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="color: #999;"></i>
                        </div>
                    </td>
                    <td>
                        {{ $technician->name }}<br>
                        <small>{{ $technician->email }}</small>
                    </td>
                    <td>
                        @if($technician->active)
                            <span style="color: green;">Active</span>
                        @else
                            <span style="color: red;">Inactive</span>
                        @endif
                    </td>
                    <td class="view-icon">
                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-500">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="action-menu">
                                <div>
                                    <a href="{{ route('mobile.technicians.edit', $technician) }}" style="color: inherit; text-decoration: none;">
                                        Edit
                                    </a>
                                </div>
                                <div>
                                    <a href="{{ route('mobile.technicians.show', $technician) }}" style="color: inherit; text-decoration: none;">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center; padding: 20px;">
        No technicians found
    </div>
@endif
@endsection 