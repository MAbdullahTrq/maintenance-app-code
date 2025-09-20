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
                For <span class="text-blue-300">small</span> to <span class="text-blue-300">medium</span> property managers
            </p>
            <p class="text-xl md:text-2xl text-white font-semibold mb-12" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                A simple set of powerful tools
            </p>
            
            <!-- Feature tiles -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 max-w-6xl mx-auto mb-12">
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
                <div class="group bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-6 border border-white border-opacity-25 hover:bg-opacity-20 transition-all duration-300 hover:scale-105 shadow-lg">
                    <div class="text-blue-200 text-3xl mb-4 group-hover:text-white transition-colors">
                        <i class="fas fa-users"></i>
                    </div>
                    <p class="text-white text-base font-medium leading-relaxed">Add team members – role based permissions</p>
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
                        @elseif(Auth::user() && method_exists(Auth::user(), 'hasTeamMemberRole') && Auth::user()->hasTeamMemberRole())
                            <a href="{{ route('mobile.manager.dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                            </a>
                        @else
                            <a href="/m/dash" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-8 sm:space-y-0 sm:space-x-12">
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
                    Whether you manage a few vacation homes or a growing portfolio of long term rental properties, 
                    <span class="text-blue-700 font-bold">Maintain</span><span class="text-black font-bold">Xtra</span> helps you track repairs, assign tasks, create reports, 
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
                            <i class="fas fa-tools text-3xl"></i>
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

    <!-- Pricing section -->
    <div class="py-16 bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-4xl font-semibold text-blue-600 tracking-wide uppercase font-weight-bold">Pricing</h2>
                <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight">
                    Simple, transparent pricing
                </p>
                <p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
                    Choose the plan that fits your property management needs. All plans include our core features with no hidden fees.
                </p>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach($plans as $index => $plan)
                        <div class="bg-white rounded-xl p-8 border {{ $plan->name === 'Standard' ? 'border-2 border-blue-600 shadow-xl relative' : 'border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300' }} flex flex-col h-full">
                            @if($plan->name === 'Standard')
                                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                    <span class="bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-semibold">Most Popular</span>
                                </div>
                            @endif
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                <p class="mt-2 text-gray-600">{{ $plan->description }}</p>
                                <div class="mt-6">
                                    <span class="text-4xl font-bold text-blue-600">{{ $plan->formatted_price }}</span>
                                    <span class="text-gray-600">/month</span>
                                </div>
                            </div>
                            <ul class="mt-8 space-y-4 flex-grow">
                                <li class="flex items-center">
                                    <i class="fas fa-gift text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-semibold">Free one month trial</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700">{{ $plan->property_limit }} Properties</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700">{{ $plan->technician_limit }} Technicians</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700">Unlimited Maintenance Requests</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700">QR Code Generation</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700">Email Notifications</span>
                                </li>
                                @if($plan->name === 'Premium')
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-3"></i>
                                        <span class="text-gray-700">Advanced Reporting & Analytics</span>
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-3"></i>
                                        <span class="text-gray-700">Priority Support</span>
                                    </li>
                                @endif
                            </ul>
                            <div class="mt-8">
                                <a href="{{ route('register') }}" class="block w-full {{ $plan->name === 'Standard' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-900 hover:bg-gray-800' }} text-white text-center py-3 px-6 rounded-lg font-semibold transition-colors">
                                    Get Started
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Additional pricing info -->
                <div class="mt-16 text-center">
                    <div class="bg-blue-50 rounded-xl p-8 border border-blue-200 max-w-7xl mx-auto">
                        <h3 class="text-2xl font-bold text-blue-900 mb-4">All Plans Include</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                                <span class="text-blue-900 font-medium">30-day free trial</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                                <span class="text-blue-900 font-medium">No setup fees</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-blue-600 mr-2"></i>
                                <span class="text-blue-900 font-medium">Mobile responsive</span>
                            </div>
                            <div class="flex items-center justify-center">
                                <i class="fas fa-headset text-blue-600 mr-2"></i>
                                <span class="text-blue-900 font-medium">24/7 support</span>
                            </div>
                        </div>
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
                        Start your 30-day free trial — no credit card required.
                    </p>
                    <div class="mt-8 flex justify-center">
                        <div class="inline-flex rounded-md shadow">
                            @auth
                                @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager())
                                    <a href="{{ route('mobile.manager.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                        Go to Dashboard
                                    </a>
                                @elseif(Auth::user() && method_exists(Auth::user(), 'isTechnician') && Auth::user()->isTechnician())
                                    <a href="{{ route('mobile.technician.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                        Go to Dashboard
                                    </a>
                                @elseif(Auth::user() && Auth::user()->hasRole('admin'))
                                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                        Go to Dashboard
                                    </a>
                                @elseif(Auth::user() && method_exists(Auth::user(), 'hasTeamMemberRole') && Auth::user()->hasTeamMemberRole())
                                    <a href="{{ route('mobile.manager.dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="/dashboard" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                        Go to Dashboard
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-900 bg-white hover:bg-blue-50">
                                    Get Started Free
                                </a>
                            @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
