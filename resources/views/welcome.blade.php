@extends('layouts.guest')

@section('title', 'Welcome')

@section('content')
<div class="relative">
    <!-- Hero section with dark overlay -->
    <div class="relative py-16 md:py-24 bg-gray-900 rounded-lg overflow-hidden">
        <!-- Background gradient with overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-indigo-900 opacity-80"></div>
        
        <!-- Content -->
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                @auth
                    Welcome back, {{ Auth::user()->name }}!
                @else
                    Simplify Your Maintenance Management
                @endauth
            </h1>
            <p class="mt-6 text-xl md:text-2xl text-white max-w-3xl mx-auto font-medium" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                Property Manager
                Handles work orders, approves requests, and assigns tasks to technicians for their properties.
                Technician
                Receives assigned tasks, completes maintenance work, and provides updates and documentation.
                Requester
                Can create a new request via a QR code or link without the need to login or create an account. The requester can also be notified and updated on the status of the maintenance request.
                Simplify your maintenance workflow
            </p>
            <div class="mt-10 flex justify-center">
                @auth
                    <div class="inline-flex rounded-md shadow">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                Go to Dashboard
                            </a>
                        @elseif(Auth::user()->isPropertyManager())
                            @if(Auth::user()->hasActiveSubscription())
                                <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('subscription.plans') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                    View Subscription Plans
                                </a>
                            @endif
                        @else
                            <a href="{{ route('technician.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                Go to Dashboard
                            </a>
                        @endif
                    </div>
                @else
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                            Get Started
                        </a>
                    </div>
                    <div class="ml-3 inline-flex">
                        <a href="#how-it-works" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-800 hover:bg-blue-700">
                            Learn more
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Features section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold text-blue-600 tracking-wide uppercase">Features</h2>
                <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight">
                    Everything you need to manage maintenance
                </p>
                <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500">
                    Whether you're a business managing multiple maintenance tasks or a service provider handling repairs, our platform ensures smooth communication and efficient task completion.
                </p>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-qrcode text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">QR Code Access</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Scan a QR code to instantly submit maintenance requests without needing to create an account.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-tasks text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Work Order Management</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Convert requests to work orders with one click and assign them to the right technician.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-bell text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Real-Time Notifications</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Keep everyone in the loop with email notifications for status updates and new assignments.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-image text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Photo Documentation</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Capture before and after photos to document work and ensure quality completion.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-comments text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Comment System</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Add notes and updates to maintenance requests to keep everyone informed of progress.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-lock text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Secure Access Control</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Role-based permissions ensure the right people have access to the right information.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How it works section -->
    <div id="how-it-works" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold text-blue-600 tracking-wide uppercase">How It Works</h2>
                <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight">
                    Simple, efficient maintenance management
                </p>
            </div>

            <div class="mt-16">
                <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-600 text-white">
                            <span class="text-xl font-bold">1</span>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-xl font-medium text-gray-900">Submit a Request</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Requesters scan a QR code or click a link to access the request page. They provide location, description, images, and optional contact information.
                            </p>
                        </div>
                    </div>

                    <div class="relative mt-10 lg:mt-0">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-600 text-white">
                            <span class="text-xl font-bold">2</span>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-xl font-medium text-gray-900">Review & Approve</h3>
                            <p class="mt-2 text-base text-gray-500">
                                The Property Manager reviews and approves the request, assigns a due date, and selects the right technician for the job.
                            </p>
                        </div>
                    </div>

                    <div class="relative mt-10 lg:mt-0">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-600 text-white">
                            <span class="text-xl font-bold">3</span>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-xl font-medium text-gray-900">Complete the Work</h3>
                            <p class="mt-2 text-base text-gray-500">
                                The assigned technician receives a notification, completes the work, and uploads photos and notes. The manager verifies completion.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User roles section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base font-semibold text-blue-600 tracking-wide uppercase">User Roles</h2>
                <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight">
                    The right access for everyone
                </p>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-user-tie text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Super Property Manager</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Oversees the entire platform and manages client accounts with full administrative access.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-user-cog text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Property Manager</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Handles work orders, approves requests, and assigns tasks to technicians for their properties.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-user-hard-hat text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Technician</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Receives assigned tasks, completes maintenance work, and provides updates and documentation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA section -->
    <div class="py-16 bg-gray-900">
        <div class="relative">
            <!-- Background gradient with overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-indigo-900 opacity-80"></div>
            
            <!-- Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                        Ready to streamline your maintenance process?
                    </h2>
                    <p class="mt-4 text-lg md:text-xl text-white font-medium" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.6);">
                        Join thousands of property managers who have simplified their maintenance workflow.
                    </p>
                    <div class="mt-8 flex justify-center">
                        <div class="inline-flex rounded-md shadow">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                Get Started Today
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
