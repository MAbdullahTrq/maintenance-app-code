<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MaintainXtra Mobile')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow p-4 flex items-center justify-between">
        <a href="@auth
            @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager())
                {{ route('mobile.manager.dashboard') }}
            @elseif(Auth::user() && method_exists(Auth::user(), 'isTechnician') && Auth::user()->isTechnician())
                {{ route('mobile.technician.dashboard') }}
            @else
                /mobile
            @endif
        @else
            /mobile
        @endauth" class="font-extrabold text-xl">
            <span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span>
        </a>
        @guest
            @yield('header-actions')
        @endguest
        @auth
        <div
            x-data="{
                open: false,
                dropdownDirection: 'down',
                updateDirection() {
                    this.$nextTick(() => {
                        const dropdown = this.$refs.dropdownMenu;
                        const button = this.$refs.dropdownButton;
                        if (dropdown && button) {
                            // Temporarily show dropdown to measure
                            dropdown.style.visibility = 'hidden';
                            dropdown.style.display = 'block';
                            const dropdownHeight = dropdown.offsetHeight;
                            dropdown.style.removeProperty('visibility');
                            dropdown.style.display = '';
                            const buttonRect = button.getBoundingClientRect();
                            const spaceBelow = window.innerHeight - buttonRect.bottom;
                            const spaceAbove = buttonRect.top;
                            if (spaceBelow >= dropdownHeight) {
                                this.dropdownDirection = 'down';
                            } else if (spaceAbove >= dropdownHeight) {
                                this.dropdownDirection = 'up';
                            } else {
                                this.dropdownDirection = spaceBelow > spaceAbove ? 'down' : 'up';
                            }
                        }
                    });
                }
            }"
            class="relative flex items-center gap-2"
            @resize.window="updateDirection()"
        >
            @php
                $isManagerDashboard = request()->routeIs('mobile.manager.dashboard');
                $isTechnicianDashboard = request()->routeIs('mobile.technician.dashboard');
            @endphp
            @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager() && !$isManagerDashboard)
                <a href="{{ route('mobile.manager.dashboard') }}" class="mr-2" title="Dashboard">
                    <img src="/icons/dash.png" alt="Dashboard" class="inline-block align-middle" style="height:28px;width:auto;vertical-align:middle;" />
                </a>
            @elseif(Auth::user() && method_exists(Auth::user(), 'isTechnician') && Auth::user()->isTechnician() && !$isTechnicianDashboard)
                <a href="{{ route('mobile.technician.dashboard') }}" class="mr-2" title="Dashboard">
                    <img src="/icons/dash.png" alt="Dashboard" class="inline-block align-middle" style="height:28px;width:auto;vertical-align:middle;" />
                </a>
            @endif
            <button
                x-ref="dropdownButton"
                @click="open = !open; if (open) updateDirection();"
                @click.away="open = false"
                class="text-sm font-medium flex items-center focus:outline-none"
            >
                {{ Auth::user()->name }} <i class="fas fa-chevron-down ml-1"></i>
            </button>
            <div
                x-show="open"
                x-transition
                x-ref="dropdownMenu"
                :class="dropdownDirection === 'down' ? 'absolute right-0 mt-2' : 'absolute right-0 mb-2 bottom-full'"
                class="w-44 bg-white rounded-lg shadow-lg py-2 z-50 border max-h-[40vh] overflow-y-auto"
                x-cloak
                style="min-width: 11rem;"
            >
                @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager())
                    <a href="/m/at" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-users text-blue-600 mr-2"></i> Technicians
                    </a>
                    <a href="/m/ap" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-building text-purple-600 mr-2"></i> Properties
                    </a>
                    <a href="{{ route('mobile.manager.all-requests') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-file-alt text-gray-700 mr-2"></i> Requests
                    </a>
                @endif
                <a href="/m/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>
        @endauth
    </header>
    @auth
    @if(Auth::user() && method_exists(Auth::user(), 'isPropertyManager') && Auth::user()->isPropertyManager())
    <nav class="bg-white shadow mb-2 rounded-b-xl">
        <div class="grid grid-cols-3 divide-x divide-gray-200 text-center">
            <!-- Property -->
            <div class="flex flex-col items-center py-3">
                <a href="{{ route('mobile.properties.index') }}" class="flex flex-col items-center group">
                    <i class="fas fa-home text-3xl text-green-600 group-hover:underline"></i>
                    <div class="font-bold text-lg mt-1">{{ isset($properties) ? $properties->count() : (isset($propertiesCount) ? $propertiesCount : 0) }}</div>
                </a>
                <a href="{{ route('mobile.properties.create') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative">
                    <span class="text-black text-2xl font-bold leading-none">+</span>
                    <span x-show="show" x-transition class="absolute left-1/2 -translate-x-1/2 mt-2 bg-white text-black text-xs px-2 py-1 rounded shadow border border-gray-200 z-10">New Property</span>
                </a>
            </div>
            <!-- Technician -->
            <div class="flex flex-col items-center py-3">
                <a href="{{ route('mobile.technicians.index') }}" class="flex flex-col items-center group">
                    <i class="fas fa-user-cog text-3xl text-gray-700 group-hover:underline"></i>
                    <div class="font-bold text-lg mt-1">{{ isset($technicians) ? $technicians->count() : (isset($techniciansCount) ? $techniciansCount : 0) }}</div>
                </a>
                <a href="{{ route('mobile.technicians.create') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative">
                    <span class="text-black text-2xl font-bold leading-none">+</span>
                    <span x-show="show" x-transition class="absolute left-1/2 -translate-x-1/2 mt-2 bg-white text-black text-xs px-2 py-1 rounded shadow border border-gray-200 z-10">New Technician</span>
                </a>
            </div>
            <!-- Request -->
            <div class="flex flex-col items-center py-3">
                <a href="{{ route('mobile.manager.all-requests') }}" class="flex flex-col items-center group">
                    <i class="fas fa-file-alt text-3xl text-gray-700 group-hover:underline"></i>
                    <div class="font-bold text-lg mt-1">{{ isset($allRequests) ? $allRequests->count() : (isset($requestsCount) ? $requestsCount : 0) }}</div>
                </a>
                <a href="{{ route('mobile.requests.create') }}" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show" class="relative">
                    <span class="text-black text-2xl font-bold leading-none">+</span>
                    <span x-show="show" x-transition class="absolute left-1/2 -translate-x-1/2 mt-2 bg-white text-black text-xs px-2 py-1 rounded shadow border border-gray-200 z-10">New Request</span>
                </a>
            </div>
        </div>
    </nav>
    @endif
    @endauth
    <main class="p-2">
        @yield('content')
    </main>
</body>
</html> 