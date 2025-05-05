@extends('mobile.layout')

@section('title', 'All Properties – Manager')

@section('content')
<div style="background:#d4ffd4;padding:4px 0;text-align:center;font-weight:bold;">All Properties - Manager</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">maintainxtra.com/m/ap/</div>
@include('mobile.manager.partials.header')
<h2 style="text-align:center;font-size:1.2em;margin:16px 0 8px;">All Properties</h2>
<input type="text" placeholder="Search" style="width:100%;margin-bottom:8px;padding:4px;">
<table style="width:100%;font-size:0.95em;">
    <thead>
        <tr style="background:#eee;">
            <th>Property</th>
            <th>Address</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($properties as $property)
        <tr>
            <td><img src="/icons/house.png" alt="" style="width:24px;height:24px;border-radius:50%;vertical-align:middle;margin-right:4px;">{{ $property->name }}</td>
            <td>{{ $property->address }}</td>
            <td>
                <div style="position:relative;">
                    <button style="background:none;border:none;font-size:1.2em;"><img src="/icons/menu.png" alt="Menu" style="width:20px;"></button>
                    <!-- Dropdown menu here -->
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection 