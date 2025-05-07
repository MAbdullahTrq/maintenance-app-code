<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - Maintenance App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Mobile-specific styles -->
    <style>
        /* Mobile-first breakpoints */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            /* Responsive logo */
            .logo-container, .logo-container a {
                width: 100% !important;
                max-width: 100% !important;
            }
            
            .logo-container img {
                display: block;
                margin-left: auto;
                margin-right: auto;
                max-width: 100% !important;
                height: auto !important;
            }
            
            /* Adjust header for very small screens */
            @media (max-width: 360px) {
                .logo-container img {
                    height: 2rem;
                }
                
                .auth-buttons {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .auth-buttons a {
                    font-size: 0.875rem;
                    padding: 0.5rem 1rem;
                    width: 100%;
                    text-align: center;
                }
            }
            
            /* Existing mobile styles */
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .btn-mobile-full {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .card {
                padding: 1rem;
            }
            
            .mobile-hide {
                display: none;
            }
            
            .mobile-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
                padding: 0.5rem;
                display: flex;
                justify-content: space-around;
                z-index: 50;
            }
        }

        @media (max-width: 400px) {
            .logo-container img {
                max-width: 120px !important;
            }
            .auth-buttons {
                flex-direction: column !important;
                gap: 0.5rem !important;
                width: 100% !important;
            }
            .auth-buttons a {
                width: 100% !important;
                text-align: center !important;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center py-4 gap-2 sm:flex-row sm:justify-between sm:items-center sm:gap-0">
                <div class="w-full flex justify-center sm:justify-start mb-2 sm:mb-0">
                    <a href="/" class="block w-full max-w-[160px] sm:max-w-none">
                        <img src="{{ asset('images/logo.png') }}" alt="Maintenance App Logo" class="block mx-auto max-h-10 sm:max-h-12 w-auto" style="max-width: 100%; height: auto;">
                    </a>
                </div>
                <nav class="w-full flex justify-center sm:justify-end">
                    @auth
                        @if(Auth::user()->isAdmin() && !Route::is('admin.dashboard'))
                            <a href="{{ route('admin.dashboard') }}" class="mr-6 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center">
                                <i class="fas fa-tachometer-alt mr-2"></i><span class="hidden sm:inline">Dashboard</span>
                            </a>
                        @elseif(Auth::user()->isPropertyManager() && !Route::is('manager.dashboard'))
                            <a href="{{ route('manager.dashboard') }}" class="mr-6 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center">
                                <i class="fas fa-tachometer-alt mr-2"></i><span class="hidden sm:inline">Dashboard</span>
                            </a>
                        @elseif(Auth::user()->isTechnician() && !Route::is('technician.dashboard'))
                            <a href="{{ route('technician.dashboard') }}" class="mr-6 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center">
                                <i class="fas fa-tachometer-alt mr-2"></i><span class="hidden sm:inline">Dashboard</span>
                            </a>
                        @endif
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                                <span class="mr-2 hidden sm:inline">{{ Auth::user()->name }}</span>
                                <i class="fas fa-user-circle sm:hidden text-xl"></i>
                                <i class="fas fa-chevron-down text-xs hidden sm:inline"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-users text-green-500 w-5 text-center"></i>
                                        <span class="ml-2">Users</span>
                                    </a>
                                @endif
                                
                                @if(Auth::user()->isPropertyManager() && Auth::user()->hasActiveSubscription())
                                    <a href="{{ route('technicians.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-users-cog text-blue-500 w-5 text-center"></i>
                                        <span class="ml-2">Technicians</span>
                                    </a>
                                    <a href="{{ route('properties.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-building text-purple-500 w-5 text-center"></i>
                                        <span class="ml-2">Properties</span>
                                    </a>
                                @endif
                                
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="auth-buttons flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-gray-100 w-full sm:w-auto text-center">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 whitespace-nowrap w-full sm:w-auto text-center">Register</a>
                        </div>
                    @endauth
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Session Messages -->
    @if(session('success') && !session('password_reset'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif
    
    <main class="flex-grow">
        @yield('content')
    </main>
    
    <footer class="bg-white shadow-inner py-6 mt-8">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Maintenance App. All rights reserved.
            </div>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html> 