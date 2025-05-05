@extends('mobile.layout')

@section('title', 'All Requests – Manager')

@section('content')
<div style="background:#d4ffd4;padding:4px 0;text-align:center;font-weight:bold;">All Requests – Manager</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">maintainxtra.com/m/ar</div>
@include('mobile.manager.partials.header')
<div style="display:flex;justify-content:space-between;margin-bottom:12px;">
    <div>Declined<br><b>{{ $counts['declined'] }}</b></div>
    <div>Assigned<br><b>{{ $counts['assigned'] }}</b></div>
    <div>Accepted<br><b>{{ $counts['accepted'] }}</b></div>
    <div>Started<br><b>{{ $counts['started'] }}</b></div>
    <div>Completed<br><b>{{ $counts['completed'] }}</b></div>
</div>
<h2 style="text-align:center;font-size:1.2em;margin:16px 0 8px;">({{ ucfirst($status) }})</h2>
<input type="text" placeholder="Search" style="width:100%;margin-bottom:8px;padding:4px;">
<table style="width:100%;font-size:0.95em;">
    <thead>
        <tr style="background:#eee;">
            <th>Property</th>
            <th>Priority &gt;</th>
            <th>Date &gt;</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($requests as $req)
        <tr>
            <td>
                {{ $req->property_name }}<br>
                <span style="font-size:0.85em;color:#888;">{{ $req->property_address }}</span>
            </td>
            <td style="background:{{ $req->priority == 'High' ? '#ff4444' : '#ffff88' }};color:#222;">
                {{ $req->priority }}
            </td>
            <td>{{ $req->date }}</td>
            <td><a href="{{ route('mobile.manager.request.show', $req->id) }}" style="color:#222;"><img src="/icons/eye.png" alt="View" style="width:20px;"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection 