@extends('layouts.guest')

@section('title', 'Welcome')

@section('content')
<div class="relative">
    <!-- Hero section with dark overlay -->
    <div class="relative py-20 md:py-32 bg-gray-900 rounded-lg overflow-hidden">
        <!-- Background gradient with overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-indigo-900 opacity-80"></div>
        
        <!-- Content -->
        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl mb-6" style="text-shadow: 0 4px 8px rgba(0,0,0,0.3);">
                Property Maintenance Management
            </h1>
            <p class="text-2xl md:text-3xl text-blue-100 max-w-4xl mx-auto font-medium mb-4" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                For <span class="text-blue-300 underline decoration-2 underline-offset-4">small</span> to <span class="text-blue-300 underline decoration-2 underline-offset-4">medium</span> property managers
            </p>
            <p class="text-xl md:text-2xl text-white font-semibold mb-12" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                A simple set of powerful tools
            </p>
            
            <!-- Feature tiles -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto mb-12">
                <div class="group bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-6 border border-white border-opacity-25 hover:bg-opacity-20 transition-all duration-300 hover:scale-105 shadow-lg">
                    <div class="text-blue-200 text-3xl mb-4 group-hover:text-white transition-colors">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <p class="text-white text-base font-medium leading-relaxed">QR codes – submitting a request has never been easier</p>
                </div>
                <div class="group bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-6 border border-white border-opacity-25 hover:bg-opacity-20 transition-all duration-300 hover:scale-105 shadow-lg">
                    <div class="text-blue-200 text-3xl mb-4 group-hover:text-white transition-colors">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <p class="text-white text-base font-medium leading-relaxed">Assign tasks – to your preferred technicians</p>
                </div>
                <div class="group bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-6 border border-white border-opacity-25 hover:bg-opacity-20 transition-all duration-300 hover:scale-105 shadow-lg">
                    <div class="text-blue-200 text-3xl mb-4 group-hover:text-white transition-colors">
                        <i class="fas fa-bell"></i>
                    </div>
                    <p class="text-white text-base font-medium leading-relaxed">Real-time notifications – keep everyone in the loop</p>
                </div>
                <div class="group bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-6 border border-white border-opacity-25 hover:bg-opacity-20 transition-all duration-300 hover:scale-105 shadow-lg">
                    <div class="text-blue-200 text-3xl mb-4 group-hover:text-white transition-colors">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <p class="text-white text-base font-medium leading-relaxed">Create reports – Admin is a breeze</p>
                </div>
                <div class="group bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-6 border border-white border-opacity-25 hover:bg-opacity-20 transition-all duration-300 hover:scale-105 shadow-lg md:col-span-2 lg:col-span-1">
                    <div class="text-blue-200 text-3xl mb-4 group-hover:text-white transition-colors">
                        <i class="fas fa-users"></i>
                    </div>
                    <p class="text-white text-base font-medium leading-relaxed">Add team members – <span class="underline decoration-blue-300 underline-offset-2">role based permissions</span></p>
                </div>
            </div
            @auth
                <div class="mt-10 flex justify-center">
                    <div class="inline-flex rounded-md shadow-lg">
                        @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager())
                            <a href="{{ route('mobile.manager.dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                            </a>
                        @elseif(Auth::user() && method_exists(Auth::user(), 'isTechnician') && Auth::user()->isTechnician())
                            <a href="{{ route('mobile.technician.dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                            </a>
                        @elseif(Auth::user() && Auth::user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                            </a>
                        @else
                            <a href="/dashboard" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-lg font-semibold rounded-xl text-white bg-transparent hover:bg-white hover:text-blue-900 transform hover:scale-105 transition-all duration-300 shadow-lg backdrop-blur-sm">
                        <i class="fas fa-sign-in-alt mr-3"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-user-plus mr-3"></i>Sign Up
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Features section -->
    <div class="py-20 bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-blue-600 mb-6 tracking-wide">BUILT FOR VACATION RENTAL MANAGERS</h2>
                <p class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight mb-8 leading-tight">
                    Everything you need to stay on top of property maintenance
                </p>
                <p class="max-w-4xl mx-auto text-xl text-gray-600 leading-relaxed">
                    Whether you manage a few vacation homes or a growing portfolio of <span class="underline decoration-blue-400 decoration-2 underline-offset-4 font-medium">long term rental properties</span>, 
                    <span class="text-red-600 underline decoration-red-400 decoration-2 underline-offset-4 font-bold">MaintainXtra</span> helps you track repairs, assign tasks, create reports, 
                    and keep things running smoothly—without the overwhelm.
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
                <h2 class="text-4xl font-semibold text-blue-600 tracking-wide uppercase font-weight-bold">How It Works</h2>
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
                                The Property Manager reviews and approves the request, and selects the right technician for the job.
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
                                The assigned technician receives a notification, completes the work, and uploads photos and notes.
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
                <h2 class="text-4xl font-semibold text-blue-600 tracking-wide uppercase font-weight-bold">User Roles</h2>
                <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight">
                    The right access for everyone
                </p>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-user-cog text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Property Manager</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Handles work orders, approves requests, and assigns tasks to technicians for their
                            properties.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-user-hard-hat text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Technician</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Receives assigned tasks, completes maintenance work, and provides updates and
                            documentation.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 shadow-sm">
                        <div class="text-blue-600 mb-4">
                            <i class="fas fa-user text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900">Requester</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Can create a new request via a QR code or link without the need to login or create
                            an account. The requester can also be notified and updated on the status of the
                            maintenance request.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA section -->
    <div class="py-16 bg-gray-900 relative">
            <!-- Background gradient with overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-indigo-900 opacity-80"></div>
            <!-- Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                        Ready to streamline your maintenance process?
                    </h2>
                    <p class="mt-4 text-lg md:text-xl text-white font-medium" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.6);">
                        Every request, task, and update easy to find, in one place.
                    </p>
                    <div class="mt-8 flex justify-center">
                        <div class="inline-flex rounded-md shadow">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                Get Started Today
                            </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
