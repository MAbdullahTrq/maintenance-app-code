@extends('mobile.layout')

@section('title', 'View Property – Manager')

@section('content')
<div style="background:#d4ffd4;padding:4px 0;text-align:center;font-weight:bold;">View Property - Manager</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">maintainxtra.com/m/p/{{ $property->id }}</div>
@include('mobile.manager.partials.header')
<div style="font-weight:bold;font-size:1.1em;background:#ff0;padding:4px 0;margin-bottom:8px;text-align:center;">{{ $property->name }}</div>
<div style="display:flex;justify-content:center;margin-bottom:8px;">
    <a href="#" style="margin:0 4px;">QR Code</a>
    <a href="#" style="margin:0 4px;">Link</a>
    <a href="{{ route('mobile.manager.property.edit', $property->id) }}" style="margin:0 4px;">Edit</a>
</div>
<div style="margin-bottom:12px;">
    <div><b>Property name</b><br>{{ $property->name }}</div>
    <div><b>Property address</b><br>{{ $property->address }}</div>
    <div><b>Special instructions</b><br>{{ $property->instructions }}</div>
    <div><b>Property image</b><br><img src="/icons/house.png" alt="Property image" style="width:100%;max-width:220px;margin-top:4px;"></div>
</div>
<h2 style="text-align:center;font-size:1.1em;margin:16px 0 8px;">Maintenance requests</h2>
<table style="width:100%;font-size:0.95em;">
    <thead>
        <tr style="background:#eee;">
            <th>Title</th>
            <th>Date created &gt;</th>
            <th>Date completed &gt;</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($property->requests as $req)
        <tr>
            <td>{{ $req->title }}</td>
            <td>{{ $req->created_at->format('d M, Y H:i') }}</td>
            <td>{{ $req->completed_at ? $req->completed_at->format('d M, Y H:i') : '-' }}</td>
            <td><a href="{{ route('mobile.manager.request.show', $req->id) }}"><img src="/icons/eye.png" alt="View" style="width:20px;"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection 