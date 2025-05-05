@extends('mobile.layout')

@section('title', 'Requests – Technician')

@section('content')
<div style="background:#ffff99;padding:4px 0;text-align:center;font-weight:bold;">Requests – Technician</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">maintainxtra.com/t/r/{{ $status }}</div>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
    <span style="font-weight:bold;font-size:1.5em;color:#222;"><span style="color:#00f;">Maintain</span>Xtra</span>
    <span style="font-size:1em;">Technician &gt;</span>
</div>
<h2 style="text-align:center;font-size:1.2em;margin:16px 0 8px;">{{ ucfirst($status) }}</h2>
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
                <img src="/icons/house.png" alt="" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;">
                {{ $req->property_name }}<br>
                <span style="font-size:0.85em;color:#888;">{{ $req->property_address }}</span>
            </td>
            <td style="background:{{ $req->priority == 'High' ? '#ff4444' : '#ffff88' }};color:#222;">
                {{ $req->priority }}
            </td>
            <td>{{ $req->date }}</td>
            <td><a href="{{ route('mobile.technician.request.show', $req->id) }}" style="color:#222;"><img src="/icons/eye.png" alt="View" style="width:20px;"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection 