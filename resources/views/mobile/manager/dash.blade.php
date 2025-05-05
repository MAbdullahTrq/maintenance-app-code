@extends('mobile.layout')

@section('title', 'Dash – Manager')

@section('content')
<div style="background:#d4ffd4;padding:4px 0;text-align:center;font-weight:bold;">Dash – Manager</div>
<div style="font-size:12px;text-align:center;margin-bottom:8px;">maintainxtra.com/m/dash</div>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
    <span style="font-weight:bold;font-size:1.5em;color:#222;"><span style="color:#00f;">Maintain</span>Xtra</span>
    <span style="font-size:1em;">Manager &gt;</span>
</div>
<div style="display:flex;justify-content:space-between;margin-bottom:12px;">
    <div style="flex:1;text-align:center;">
        <div><img src="/icons/house.png" alt="Properties" style="width:32px;"></div>
        <div>0</div>
        <div><img src="/icons/plus.png" alt="Add" style="width:18px;"></div>
    </div>
    <div style="flex:1;text-align:center;">
        <div><img src="/icons/technician.png" alt="Technicians" style="width:32px;"></div>
        <div>1</div>
        <div><img src="/icons/plus.png" alt="Add" style="width:18px;"></div>
    </div>
    <div style="flex:1;text-align:center;">
        <div><img src="/icons/all_requests.png" alt="Requests" style="width:32px;"></div>
        <div>2</div>
        <div><img src="/icons/plus.png" alt="Add" style="width:18px;"></div>
    </div>
</div>
<h2 style="text-align:center;font-size:1.2em;margin:16px 0 8px;">Pending Requests</h2>
@if(count($pendingRequests) > 0)
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
        @foreach($pendingRequests as $req)
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
@else
<div style="text-align:center;color:#888;">You have no new Pending Requests</div>
@endif
@endsection 