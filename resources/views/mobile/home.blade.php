@extends('mobile.layout')

@section('title', 'Mobile Home')

@section('header-actions')
    @guest
        <a href="{{ route('login') }}" class="text-blue-700 font-semibold px-4 py-2 rounded hover:underline">Login</a>
    @endguest
@endsection

@section('content')
<div class="bg-blue-700 text-white text-center py-8 rounded-b-3xl shadow">
    <h1 class="text-2xl font-bold mb-2">
        @auth
            Welcome back, {{ Auth::user()->name }}!
        @else
            Simplify Your Maintenance Management
        @endauth
    </h1>
    <p class="text-base mb-4">Simplify your maintenance workflow</p>
    <div class="flex flex-col items-center gap-2">
        @auth
            <a href="{{ route('mobile.manager.dashboard') }}" class="bg-white text-blue-700 font-semibold px-6 py-2 rounded shadow">Go to Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="bg-white text-blue-700 font-semibold px-6 py-2 rounded shadow">Login</a>
            <a href="{{ route('mobile.register') }}" class="bg-blue-900 text-white font-semibold px-6 py-2 rounded shadow">Register</a>
        @endauth
    </div>
</div>

<div class="p-4">
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <h2 class="text-blue-700 text-lg font-bold mb-2 text-center">Features</h2>
        <ul class="space-y-2">
            <li class="flex items-center gap-2"><i class="fas fa-qrcode text-blue-600"></i>QR Code Access</li>
            <li class="flex items-center gap-2"><i class="fas fa-tasks text-blue-600"></i>Work Order Management</li>
            <li class="flex items-center gap-2"><i class="fas fa-bell text-blue-600"></i>Real-Time Notifications</li>
            <li class="flex items-center gap-2"><i class="fas fa-image text-blue-600"></i>Photo Documentation</li>
            <li class="flex items-center gap-2"><i class="fas fa-comments text-blue-600"></i>Comment System</li>
            <li class="flex items-center gap-2"><i class="fas fa-lock text-blue-600"></i>Secure Access Control</li>
        </ul>
    </div>

    <div id="how-it-works" class="bg-white rounded-xl shadow p-4 mb-6">
        <h2 class="text-blue-700 text-lg font-bold mb-2 text-center">How It Works</h2>
        <ol class="list-decimal pl-5 space-y-2">
            <li><span class="font-semibold">Submit a Request:</span> Scan a QR code or click a link to access the request page. Provide location, description, images, and contact info.</li>
            <li><span class="font-semibold">Review & Approve:</span> The Property Manager reviews, approves, assigns a due date, and selects a technician.</li>
            <li><span class="font-semibold">Complete the Work:</span> The technician receives a notification, completes the work, uploads photos and notes. The manager verifies completion.</li>
        </ol>
    </div>

    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <h2 class="text-blue-700 text-lg font-bold mb-2 text-center">User Roles</h2>
        <ul class="space-y-2">
            <li><span class="font-semibold">Admin:</span> Manage users, properties, and subscriptions.</li>
            <li><span class="font-semibold">Property Manager:</span> Oversee requests, assign technicians, and track progress.</li>
            <li><span class="font-semibold">Technician:</span> View and complete assigned tasks.</li>
        </ul>
    </div>
</div>
@endsection 