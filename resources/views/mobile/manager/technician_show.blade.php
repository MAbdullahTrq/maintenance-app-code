@extends('mobile.layout')

@section('title', 'View Technician – Manager')

@section('content')
<div style="background:#d4ffd4;padding:4px 0;text-align:center;font-weight:bold;">View Technician - Manager</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">maintainxtra.com/m/t/{{ $technician->id }}</div>
@include('mobile.manager.partials.header')
<div style="font-weight:bold;font-size:1.1em;background:#ff0;padding:4px 0;margin-bottom:8px;text-align:center;">
    {{ $technician->name }}
</div>
<div style="margin-bottom:12px;">
    <div><b>Property name</b><br>{{ $technician->property_name }}</div>
    <div><b>Email</b><br><a href="mailto:{{ $technician->email }}">{{ $technician->email }}</a></div>
    <div><b>Phone</b><br>{{ $technician->phone }}</div>
    <div>
        <img src="/icons/technician.png" alt="Technician image" style="width:100%;max-width:220px;margin-top:4px;">
    </div>
</div>
<h2 style="text-align:center;font-size:1.1em;margin:16px 0 8px;">Maintenance requests</h2>
<table style="width:100%;font-size:0.95em;">
    <thead>
        <tr style="background:#eee;">
            <th>Property</th>
            <th>Date started &gt;</th>
            <th>Date completed &gt;</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($technician->requests as $req)
        <tr>
            <td>
                {{ $req->property_name }}<br>
                <span style="font-size:0.85em;color:#888;">{{ $req->property_address }}</span>
            </td>
            <td>{{ $req->started_at ? $req->started_at->format('d M, Y H:i') : '-' }}</td>
            <td>{{ $req->completed_at ? $req->completed_at->format('d M, Y H:i') : '-' }}</td>
            <td><a href="{{ route('mobile.manager.request.show', $req->id) }}"><img src="/icons/eye.png" alt="View" style="width:20px;"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection 