@extends('mobile.layout')

@section('title', 'Welcome – MaintainXtra')

@section('content')
<div style="background:#e3f7ff;padding:12px 0;text-align:center;font-weight:bold;font-size:1.2em;">
    <img src="/icons/dash.png" alt="Logo" style="width:48px;vertical-align:middle;margin-right:8px;">
    MaintainXtra
</div>
<div style="margin:24px 0;text-align:center;">
    <div style="font-size:1.1em;margin-bottom:12px;">Welcome to MaintainXtra!</div>
    <div style="color:#555;font-size:0.98em;margin-bottom:24px;">
        Please select your role to continue:
    </div>
    <a href="{{ route('mobile.manager.dash') }}" style="display:block;margin:12px auto 8px auto;padding:12px 0;width:90%;max-width:320px;background:#d4ffd4;color:#222;font-weight:bold;border-radius:8px;text-decoration:none;font-size:1.1em;">
        <img src="/icons/house.png" alt="Manager" style="width:24px;vertical-align:middle;margin-right:8px;"> Manager Dashboard
    </a>
    <a href="{{ route('mobile.technician.dash') }}" style="display:block;margin:8px auto 0 auto;padding:12px 0;width:90%;max-width:320px;background:#ffff99;color:#222;font-weight:bold;border-radius:8px;text-decoration:none;font-size:1.1em;">
        <img src="/icons/technician.png" alt="Technician" style="width:24px;vertical-align:middle;margin-right:8px;"> Technician Dashboard
    </a>
    <a href="/login" style="display:block;margin:18px auto 0 auto;padding:10px 0;width:90%;max-width:320px;background:#e3e3e3;color:#222;font-weight:bold;border-radius:8px;text-decoration:none;font-size:1em;">
        Login
    </a>
    <a href="/register" style="display:block;margin:8px auto 0 auto;padding:10px 0;width:90%;max-width:320px;background:#e3e3e3;color:#222;font-weight:bold;border-radius:8px;text-decoration:none;font-size:1em;">
        Register
    </a>
</div>
<div style="text-align:center;margin-top:16px;">
    <a href="/help" style="color:#007bff;text-decoration:underline;font-size:1em;">Need Help?</a>
</div>
<div style="text-align:center;margin-top:18px;">
    @if(!session('force_desktop'))
        <form method="POST" action="{{ route('force.desktop') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:none;border:none;color:#007bff;text-decoration:underline;font-size:1em;">View Desktop Site</button>
        </form>
    @else
        <form method="POST" action="{{ route('force.mobile') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:none;border:none;color:#007bff;text-decoration:underline;font-size:1em;">Switch to Mobile Site</button>
        </form>
    @endif
</div>
<div style="text-align:center;color:#aaa;font-size:0.95em;margin-top:32px;">
    &copy; {{ date('Y') }} MaintainXtra. All rights reserved.
</div>
@endsection 