<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - Maintenance App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Vite Assets with Fallback -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback for when Vite is failing in dev mode -->
    @if(config('app.env') === 'local')
    <script>
        // Check if Vite styles loaded, if not use fallback
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const appStyles = document.querySelector('[data-vite]');
                if (!appStyles || !document.body.classList.contains('bg-gray-100')) {
                    const fallbackStyles = document.createElement('link');
                    fallbackStyles.rel = 'stylesheet';
                    fallbackStyles.href = '{{ asset('build/assets/app.css') }}';
                    document.head.appendChild(fallbackStyles);
                    
                    const fallbackScript = document.createElement('script');
                    fallbackScript.src = '{{ asset('build/assets/app.js') }}';
                    document.body.appendChild(fallbackScript);
                    
                    console.warn('Vite assets not loaded, using fallback assets');
                }
            }, 500);
        });
    </script>
    
    <!-- Critical CSS to ensure basic layout even if Vite fails -->
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        header {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        main {
            flex-grow: 1;
        }
        
        footer {
            background-color: white;
            padding: 1.5rem 0;
            margin-top: 2rem;
            text-align: center;
            color: #6b7280;
        }
        
        nav {
            display: flex;
            align-items: center;
        }
        
        a {
            text-decoration: none;
            color: #374151;
        }
        
        .badge {
            display: inline-flex;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
    @endif
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
    <script>
        // Store screen width in a cookie for mobile detection
        document.cookie = 'screen_width=' + window.innerWidth + '; path=/';
        window.addEventListener('resize', function() {
            document.cookie = 'screen_width=' + window.innerWidth + '; path=/';
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white shadow">
        <div class="w-full flex flex-col items-center justify-center py-4 space-y-2 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 sm:space-x-4">
            <a href="/" class="block w-full flex justify-center sm:w-auto">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Maintenance App Logo"
                     class="h-10 sm:h-12 w-auto max-w-[140px] sm:max-w-none object-contain mx-auto" />
            </a>
            <nav class="w-full flex flex-col items-center gap-2 sm:flex-row sm:w-auto sm:gap-2">
                @auth
                    @if(Auth::user()->isAdmin() && !Route::is('admin.dashboard'))
                        <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto text-gray-700 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-gray-100 text-center flex items-center justify-center"><i class="fas fa-tachometer-alt mr-2"></i><span>Dashboard</span></a>
                    @elseif(Auth::user()->isPropertyManager() && !Route::is('manager.dashboard'))
                        <a href="{{ route('manager.dashboard') }}" class="w-full sm:w-auto text-gray-700 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-gray-100 text-center flex items-center justify-center"><i class="fas fa-tachometer-alt mr-2"></i><span>Dashboard</span></a>
                    @elseif(Auth::user()->isTechnician() && !Route::is('technician.dashboard'))
                        <a href="{{ route('technician.dashboard') }}" class="w-full sm:w-auto text-gray-700 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-gray-100 text-center flex items-center justify-center"><i class="fas fa-tachometer-alt mr-2"></i><span>Dashboard</span></a>
                    @endif
                    <div class="relative w-full sm:w-auto flex justify-center sm:justify-end" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none w-full sm:w-auto justify-center">
                            <span class="mr-2">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10" style="left: 0; right: 0; margin-left: auto; margin-right: auto; top: 100%;">
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
                    <a href="{{ route('login') }}" class="w-full sm:w-auto text-gray-700 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-gray-100 text-center">Login</a>
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 whitespace-nowrap text-center">Register</a>
                @endauth
            </nav>
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