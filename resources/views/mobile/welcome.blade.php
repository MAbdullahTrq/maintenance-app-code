@extends('layouts.mobile')

@section('title', 'Welcome')

@section('content')
<div class="flex flex-col items-center pt-8 pb-12 px-4">
    <div class="bg-blue-800 rounded-lg w-full p-6 mb-8 text-center">
        <h1 class="text-3xl font-bold text-white mb-4">
            @auth
                Welcome back, {{ Auth::user()->name }}!
            @else
                Welcome to MaintainXtra
            @endauth
        </h1>
        <p class="text-xl text-white mb-6">
            Simplify your maintenance workflow
        </p>
        
        <div class="mt-6">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 w-full border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                        <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                    </a>
                @elseif(Auth::user()->isPropertyManager())
                    @if(Auth::user()->hasActiveSubscription())
                        <a href="{{ route('mobile.manager.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 w-full border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                            <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('subscription.plans') }}" class="inline-flex items-center justify-center px-5 py-3 w-full border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                            <i class="fas fa-crown mr-2"></i>View Subscription Plans
                        </a>
                    @endif
                @else
                    <a href="{{ route('mobile.technician.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 w-full border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                        <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                    </a>
                @endif
            @else
                <div class="flex flex-col space-y-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                </div>
            @endauth
        </div>
    </div>
    
    <h2 class="text-2xl font-bold text-center text-gray-800 my-6">Key Features</h2>
    
    <div class="grid grid-cols-2 gap-4 w-full">
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <i class="fas fa-qrcode text-2xl text-blue-600 mb-2"></i>
            <h3 class="font-semibold">QR Code Access</h3>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <i class="fas fa-tasks text-2xl text-blue-600 mb-2"></i>
            <h3 class="font-semibold">Work Orders</h3>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <i class="fas fa-bell text-2xl text-blue-600 mb-2"></i>
            <h3 class="font-semibold">Notifications</h3>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <i class="fas fa-image text-2xl text-blue-600 mb-2"></i>
            <h3 class="font-semibold">Photo Docs</h3>
        </div>
    </div>
    
    <div class="mt-8 w-full">
        <div class="bg-gray-100 rounded-lg p-5">
            <h3 class="font-bold text-xl mb-3 text-center">How It Works</h3>
            
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-blue-600 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3">
                    <span class="font-bold">1</span>
                </div>
                <div>
                    <p class="font-medium">Submit a maintenance request</p>
                </div>
            </div>
            
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-blue-600 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3">
                    <span class="font-bold">2</span>
                </div>
                <div>
                    <p class="font-medium">Manager reviews & assigns</p>
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-600 text-white rounded-full h-8 w-8 flex items-center justify-center mr-3">
                    <span class="font-bold">3</span>
                </div>
                <div>
                    <p class="font-medium">Technician completes work</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 