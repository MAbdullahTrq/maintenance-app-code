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
        <a href="{{ route('mobile.manager.dashboard') }}" class="font-extrabold text-xl"><span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span></a>
        @auth
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="text-sm font-medium flex items-center focus:outline-none">
                {{ Auth::user()->name }} <i class="fas fa-chevron-down ml-1"></i>
            </button>
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg py-2 z-50 border">
                <a href="/m/at" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-users text-blue-600 mr-2"></i> Technicians
                </a>
                <a href="/m/ap" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-building text-purple-600 mr-2"></i> Properties
                </a>
                <a href="{{ route('mobile.manager.all-requests') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-file-alt text-gray-700 mr-2"></i> Requests
                </a>
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
    <nav class="bg-white shadow mb-2 rounded-b-xl">
        <div class="grid grid-cols-3 divide-x divide-gray-200 text-center">
            <a href="{{ route('mobile.properties.index') }}" class="block py-2">
                <div class="flex flex-col items-center">
                    <i class="fas fa-home text-2xl text-green-600"></i>
                    <span class="text-xs font-bold text-gray-600 mt-1">{{ isset($properties) ? $properties->count() : (isset($propertiesCount) ? $propertiesCount : 0) }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.technicians.index') }}" class="block py-2">
                <div class="flex flex-col items-center">
                    <i class="fas fa-user-cog text-2xl text-gray-700"></i>
                    <span class="text-xs font-bold text-gray-600 mt-1">{{ isset($technicians) ? $technicians->count() : (isset($techniciansCount) ? $techniciansCount : 0) }}</span>
                </div>
            </a>
            <a href="{{ route('mobile.manager.all-requests') }}" class="block py-2">
                <div class="flex flex-col items-center">
                    <i class="fas fa-file-alt text-2xl text-gray-700"></i>
                    <span class="text-xs font-bold text-gray-600 mt-1">{{ isset($allRequests) ? $allRequests->count() : (isset($requestsCount) ? $requestsCount : 0) }}</span>
                </div>
            </a>
        </div>
    </nav>
    @endauth
    <main class="p-2">
        @yield('content')
    </main>
</body>
</html> 