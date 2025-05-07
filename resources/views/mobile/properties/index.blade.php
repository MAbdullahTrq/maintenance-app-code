@extends('layouts.mobile')

@section('title', 'All Properties')

@php
$pageTitle = 'All Properties - Manager';
$pageUrl = 'maintainxtra.com/m/ap/';
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
<div class="section-title">All Properties</div>

<div class="search-bar">
    <input type="text" placeholder="Search">
</div>

<table class="data-table">
    <thead>
        <tr>
            <th></th>
            <th>Property</th>
            <th>Address</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($properties as $property)
            <tr>
                <td style="width: 40px;">
                    @if($property->image)
                        <img src="{{ asset('storage/' . $property->image) }}" alt="{{ $property->name }}" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #eee; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-building" style="color: #999;"></i>
                        </div>
                    @endif
                </td>
                <td>{{ $property->name }}</td>
                <td>{{ $property->address }}</td>
                <td class="view-icon">
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-500">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" class="action-menu">
                            <div>
                                <a href="{{ route('mobile.properties.edit', $property) }}" style="color: inherit; text-decoration: none;">
                                    Edit
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('mobile.properties.show', $property) }}" style="color: inherit; text-decoration: none;">
                                    View
                                </a>
                            </div>
                            <div>
                                <a href="#" style="color: inherit; text-decoration: none;">
                                    QR Code
                                </a>
                            </div>
                            <div>
                                <a href="#" style="color: inherit; text-decoration: none;">
                                    Link
                                </a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection 