<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MaintainXtra') }} - @yield('title', 'Home')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MaintainXtra">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Cloudflare Turnstile -->
    <script>
        window.onTurnstileLoad = function () {
            // Auto-render all cf-turnstile elements
            document.querySelectorAll('.cf-turnstile').forEach(function(element) {
                if (!element.hasAttribute('data-rendered')) {
                    const size = element.getAttribute('data-size') || 'normal';
                    const config = {
                        sitekey: element.getAttribute('data-sitekey'),
                        theme: element.getAttribute('data-theme') || 'light',
                        size: size
                    };
                    
                    console.log('Rendering Turnstile with config:', config);
                    console.log('Element size attribute:', size);
                    
                    // For flexible size, ensure proper container styling
                    if (size === 'flexible') {
                        // Let Turnstile handle sizing, just ensure container is ready
                        const parent = element.closest('.turnstile-container');
                        if (parent) {
                            parent.style.display = 'flex';
                            parent.style.justifyContent = 'center';
                            parent.style.width = '100%';
                        }
                        
                        // Desktop should use full normal size
                        if (window.innerWidth > 480) {
                            // Let flexible size handle desktop sizing naturally
                        } else {
                            // Mobile fallback
                            element.style.maxWidth = 'calc(100vw - 40px)';
                        }
                    }
                    
                    try {
                        const widgetId = turnstile.render(element, config);
                        console.log('Turnstile widget rendered with ID:', widgetId);
                        element.setAttribute('data-rendered', 'true');
                        element.setAttribute('data-widget-id', widgetId);
                    } catch (error) {
                        console.error('Error rendering Turnstile:', error);
                    }
                }
            });
        };
        
        // Force refresh when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit then check if widgets need re-rendering
            setTimeout(function() {
                if (typeof turnstile !== 'undefined') {
                    window.onTurnstileLoad();
                }
            }, 1000);
        });
    </script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&render=explicit&t={{ time() }}" async defer></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="@auth
                                @if(Auth::user() && Auth::user()->isAdmin())
                                    {{ route('admin.dashboard') }}
                                @elseif(Auth::user() && Auth::user()->isPropertyManager())
                                    {{ route('manager.dashboard') }}
                                @elseif(Auth::user() && Auth::user()->isTechnician())
                                    {{ route('technician.dashboard') }}
                                @elseif(Auth::user() && method_exists(Auth::user(), 'hasTeamMemberRole') && Auth::user()->hasTeamMemberRole())
                                    {{ route('manager.dashboard') }}
                                @else
                                    {{ route('dashboard') }}
                                @endif
                            @else
                                /
                            @endauth" class="flex items-center">
                                <span class="font-extrabold text-xl md:text-2xl lg:text-3xl">
                                    <span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @auth
                            <span class="text-gray-700 mr-4">Welcome, {{ Auth::user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-grow">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @elseif(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} MaintainXtra. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html> 