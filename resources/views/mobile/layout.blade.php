<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MaintainXtra Mobile')</title>
    
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
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
                        
                        // Mobile-specific adjustments
                        if (window.innerWidth <= 480) {
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
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow p-4 md:p-6 lg:p-8 flex items-center justify-between">
        <a href="@auth
            @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager())
                {{ route('mobile.manager.dashboard') }}
            @elseif(Auth::user() && method_exists(Auth::user(), 'isTechnician') && Auth::user()->isTechnician())
                {{ route('mobile.technician.dashboard') }}
            @elseif(Auth::user() && method_exists(Auth::user(), 'hasTeamMemberRole') && Auth::user()->hasTeamMemberRole())
                {{ route('mobile.manager.dashboard') }}
            @else
                /
            @endif
        @else
            /
        @endauth" class="font-extrabold text-xl md:text-2xl lg:text-3xl">
            <span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span>
        </a>
        @guest
            @yield('header-actions')
        @endguest
        @auth
        <div
            x-data="{
                open: false,
                popperInstance: null,
                initPopper() {
                    this.$nextTick(() => {
                        if (this.open) {
                            if (this.popperInstance) {
                                this.popperInstance.destroy();
                            }
                            this.popperInstance = Popper.createPopper(this.$refs.dropdownButton, this.$refs.dropdownMenu, {
                                placement: 'bottom-end',
                                modifiers: [
                                    { name: 'flip', options: { fallbackPlacements: ['top-end', 'bottom-end'] } },
                                    { name: 'preventOverflow', options: { boundary: 'viewport' } },
                                ],
                            });
                        } else if (this.popperInstance) {
                            this.popperInstance.destroy();
                            this.popperInstance = null;
                        }
                    });
                }
            }"
            class="relative flex items-center gap-2"
        >
            @php
                $isManagerDashboard = request()->routeIs('mobile.manager.dashboard');
                $isTechnicianDashboard = request()->routeIs('mobile.technician.dashboard');
            @endphp
            @if((Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager()) || (Auth::user() && method_exists(Auth::user(), 'hasTeamMemberRole') && Auth::user()->hasTeamMemberRole()) && !$isManagerDashboard)
                <a href="{{ route('mobile.manager.dashboard') }}" class="mr-2 md:mr-3 lg:mr-4" title="Dashboard">
                    <img src="/icons/dash.png" alt="Dashboard" class="inline-block align-middle w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10" />
                </a>
            @elseif(Auth::user() && method_exists(Auth::user(), 'isTechnician') && Auth::user()->isTechnician() && !$isTechnicianDashboard)
                <a href="{{ route('mobile.technician.dashboard') }}" class="mr-2 md:mr-3 lg:mr-4" title="Dashboard">
                    <img src="/icons/dash.png" alt="Dashboard" class="inline-block align-middle w-7 h-7 md:w-8 md:h-8 lg:w-10 lg:h-10" />
                </a>
            @endif
            <button
                x-ref="dropdownButton"
                @click="open = !open; initPopper();"
                @click.away="open = false; if (popperInstance) { popperInstance.destroy(); popperInstance = null; }"
                class="text-sm md:text-base lg:text-lg font-medium flex items-center focus:outline-none"
            >
                {{ Auth::user()->name }} <i class="fas fa-chevron-down ml-1 md:ml-2"></i>
            </button>
            <div
                x-show="open"
                x-transition
                x-ref="dropdownMenu"
                class="absolute right-0 w-44 md:w-48 lg:w-52 bg-white rounded-lg shadow-lg py-2 z-50 border max-h-[40vh] overflow-y-auto"
                x-cloak
                style="min-width: 11rem;"
            >
                @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager())
                    <a href="/m/ao" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Owners
                    </a>
                    <a href="/m/ap" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Properties
                    </a>
                    <a href="/m/at" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Technicians
                    </a>
                    <a href="/m/cl" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Checklists
                    </a>
                    <a href="{{ route('mobile.manager.all-requests') }}" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Requests
                    </a>
                    
                    <!-- Team Management Section -->
                    <div class="border-t border-gray-200 my-1"></div>
                    <a href="{{ route('mobile.team.index') }}" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Team Assistants
                    </a>
                    <a href="{{ route('mobile.team.create') }}" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Invite Assistant
                    </a>
                @elseif(Auth::user() && method_exists(Auth::user(), 'hasTeamMemberRole') && Auth::user()->hasTeamMemberRole())
                    <a href="/m/ao" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Owners
                    </a>
                    <a href="/m/ap" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Properties
                    </a>
                    <a href="/m/at" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Technicians
                    </a>
                    <a href="/m/cl" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Checklists
                    </a>
                    <a href="{{ route('mobile.manager.all-requests') }}" class="flex items-center px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">
                        Requests
                    </a>
                @endif
                <div class="border-t border-gray-200 my-1"></div>
                <a href="/m/profile" class="block px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm md:text-base text-gray-700 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>
        @endauth
    </header>
    @auth
    @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager() || 
        Auth::user() && method_exists(Auth::user(), 'hasTeamMemberRole') && Auth::user()->hasTeamMemberRole())
    <nav class="bg-white shadow mb-2 rounded-b-xl">
        <div class="grid grid-cols-4 divide-x divide-gray-200 text-center md:py-2">
            <!-- Owners -->
            <div class="flex flex-col items-center py-3 md:py-4">
                <a href="/m/ao" class="flex flex-col items-center group">
                    <i class="fas fa-user text-2xl md:text-3xl lg:text-4xl text-yellow-600 group-hover:underline"></i>
                    <div class="font-bold text-sm md:text-lg lg:text-xl mt-1">{{ $ownersCount ?? 0 }}</div>
                </a>
                @if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
                    <a href="/m/ao/create" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative mt-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="text-black text-xl md:text-2xl lg:text-3xl font-bold leading-none">+</span>
                        <span x-show="show" x-transition class="absolute right-full top-1/2 -translate-y-1/2 mr-2 bg-white text-black text-xs md:text-sm px-2 py-1 rounded shadow border border-gray-200 z-10 whitespace-nowrap">New Owner</span>
                    </a>
                @elseif(Auth::user()->isViewer())
                    <!-- Viewers see no add button -->
                @else
                    <a href="{{ route('mobile.subscription.plans') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative mt-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="text-gray-400 text-xl md:text-2xl lg:text-3xl font-bold leading-none">🔒</span>
                        <span x-show="show" x-transition class="absolute right-full top-1/2 -translate-y-1/2 mr-2 bg-white text-black text-xs md:text-sm px-2 py-1 rounded shadow border border-gray-200 z-10 whitespace-nowrap">Subscription Required</span>
                    </a>
                @endif
            </div>
            <!-- Property -->
            <div class="flex flex-col items-center py-3 md:py-4">
                <a href="{{ route('mobile.properties.index') }}" class="flex flex-col items-center group">
                    <i class="fas fa-home text-2xl md:text-3xl lg:text-4xl text-green-600 group-hover:underline"></i>
                    <div class="font-bold text-sm md:text-lg lg:text-xl mt-1">{{ $propertiesCount ?? 0 }}</div>
                </a>
                @if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
                    <a href="{{ route('mobile.properties.create') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative mt-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="text-black text-xl md:text-2xl lg:text-3xl font-bold leading-none">+</span>
                        <span x-show="show" x-transition class="absolute right-full top-1/2 -translate-y-1/2 mr-2 bg-white text-black text-xs md:text-sm px-2 py-1 rounded shadow border border-gray-200 z-10 whitespace-nowrap">New Property</span>
                    </a>
                @elseif(Auth::user()->isViewer())
                    <!-- Viewers see no add button -->
                @else
                    <a href="{{ route('mobile.subscription.plans') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative mt-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="text-gray-400 text-xl md:text-2xl lg:text-3xl font-bold leading-none">🔒</span>
                        <span x-show="show" x-transition class="absolute right-full top-1/2 -translate-y-1/2 mr-2 bg-white text-black text-xs md:text-sm px-2 py-1 rounded shadow border border-gray-200 z-10 whitespace-nowrap">Subscription Required</span>
                    </a>
                @endif
            </div>
            <!-- Technician -->
            <div class="flex flex-col items-center py-3 md:py-4">
                <a href="{{ route('mobile.technicians.index') }}" class="flex flex-col items-center group">
                    <i class="fas fa-user-cog text-2xl md:text-3xl lg:text-4xl text-gray-700 group-hover:underline"></i>
                    <div class="font-bold text-sm md:text-lg lg:text-xl mt-1">{{ $techniciansCount ?? 0 }}</div>
                </a>
                @if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
                    <a href="{{ route('mobile.technicians.create') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative mt-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="text-black text-xl md:text-2xl lg:text-3xl font-bold leading-none">+</span>
                        <span x-show="show" x-transition class="absolute left-full top-1/2 -translate-y-1/2 ml-2 bg-white text-black text-xs md:text-sm px-2 py-1 rounded shadow border border-gray-200 z-10 whitespace-nowrap">New Technician</span>
                    </a>
                @elseif(Auth::user()->isViewer())
                    <!-- Viewers see no add button -->
                @else
                    <a href="{{ route('mobile.subscription.plans') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative mt-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="text-gray-400 text-xl md:text-2xl lg:text-3xl font-bold leading-none">🔒</span>
                        <span x-show="show" x-transition class="absolute left-full top-1/2 -translate-y-1/2 ml-2 bg-white text-black text-xs md:text-sm px-2 py-1 rounded shadow border border-gray-200 z-10 whitespace-nowrap">Subscription Required</span>
                    </a>
                @endif
            </div>
            <!-- Request -->
            <div class="flex flex-col items-center py-3 md:py-4">
                <a href="{{ route('mobile.manager.all-requests') }}" class="flex flex-col items-center group">
                    <i class="fas fa-file-alt text-2xl md:text-3xl lg:text-4xl text-gray-700 group-hover:underline"></i>
                    <div class="font-bold text-sm md:text-lg lg:text-xl mt-1">{{ $requestsCount ?? 0 }}</div>
                </a>
                @if(!Auth::user()->isViewer())
                    <a href="{{ route('mobile.requests.create') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative mt-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="text-black text-xl md:text-2xl lg:text-3xl font-bold leading-none">+</span>
                        <span x-show="show" x-transition class="absolute right-full top-1/2 -translate-y-1/2 mr-2 bg-white text-black text-xs md:text-sm px-2 py-1 rounded shadow border border-gray-200 z-10 whitespace-nowrap">New Request</span>
                    </a>
                @endif
            </div>
        </div>
    </nav>
    @endif
    @endauth
    
    <!-- Session Messages -->
    @if(session('success'))
        <div class="p-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button @click="show = false" class="absolute top-0 right-0 px-4 py-3">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button @click="show = false" class="absolute top-0 right-0 px-4 py-3">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
    
    <main class="p-2">
        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html> 