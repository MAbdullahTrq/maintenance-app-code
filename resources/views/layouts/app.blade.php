<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    
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
    
    <title>@yield('title') - MaintainXtra</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
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
                
                <nav class="flex items-center">
                    @auth
                        @if(Auth::user()->isPropertyManager() && Auth::user()->hasActiveSubscription())
                            <a href="{{ route('team.index') }}" class="mr-6 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center">
                                <i class="fas fa-users mr-2"></i>Team
                            </a>
                        @endif
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                                <span class="mr-2">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-users text-green-500 w-5 text-center"></i>
                                        <span class="ml-2">Users</span>
                                    </a>
                                @endif
                                
                                @if(Auth::user()->isPropertyManager() && Auth::user()->hasActiveSubscription())
                                    <!-- Management Section -->
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                        Management
                                    </div>
                                    <a href="{{ route('owners.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user text-yellow-500 w-5 text-center"></i>
                                        <span class="ml-2">Owners</span>
                                    </a>
                                    <a href="{{ route('properties.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-building text-purple-500 w-5 text-center"></i>
                                        <span class="ml-2">Properties</span>
                                    </a>
                                    <a href="{{ route('technicians.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-users-cog text-blue-500 w-5 text-center"></i>
                                        <span class="ml-2">Technicians</span>
                                    </a>
                                    <a href="{{ route('checklists.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-clipboard-list text-orange-500 w-5 text-center"></i>
                                        <span class="ml-2">Checklists</span>
                                    </a>
                                    
                                    <!-- Team Settings Section -->
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                        Team Settings
                                    </div>
                                    <a href="{{ route('team.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-users text-green-500 w-5 text-center"></i>
                                        <span class="ml-2">Team Assistants</span>
                                    </a>
                                    <a href="{{ route('team.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user-plus text-blue-500 w-5 text-center"></i>
                                        <span class="ml-2">Invite Assistant</span>
                                    </a>
                                @endif
                                
                                <!-- Account Section -->
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                    Account
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-cog text-gray-500 w-5 text-center"></i>
                                    <span class="ml-2">Profile</span>
                                </a>
                                
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt text-red-500 w-5 text-center"></i>
                                        <span class="ml-2">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 mr-4">Login</a>
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Login</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Session Messages -->
    @if(session('success'))
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
                &copy; {{ date('Y') }} MaintainXtra. All rights reserved.
            </div>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html> 