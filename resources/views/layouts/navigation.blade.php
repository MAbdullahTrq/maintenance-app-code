<!-- Desktop Navigation -->
<nav class="bg-white border-b border-gray-100 hidden md:block">
    // ... existing desktop navigation code ...
</nav>

<!-- Mobile Navigation -->
<nav class="mobile-nav md:hidden">
    @if(auth()->user()->isManager())
        <a href="{{ route('manager.dashboard') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('manager.dashboard') ? 'text-blue-500' : '' }}">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs mt-1">Dashboard</span>
        </a>
        <a href="{{ route('properties.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('properties.*') ? 'text-blue-500' : '' }}">
            <i class="fas fa-building text-xl"></i>
            <span class="text-xs mt-1">Properties</span>
        </a>
        <a href="{{ route('technicians.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('technicians.*') ? 'text-blue-500' : '' }}">
            <i class="fas fa-users text-xl"></i>
            <span class="text-xs mt-1">Technicians</span>
        </a>
        <a href="{{ route('maintenance.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('maintenance.*') ? 'text-blue-500' : '' }}">
            <i class="fas fa-wrench text-xl"></i>
            <span class="text-xs mt-1">Requests</span>
        </a>
    @endif

    @if(auth()->user()->isTechnician())
        <a href="{{ route('technician.dashboard') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('technician.dashboard') ? 'text-blue-500' : '' }}">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs mt-1">Dashboard</span>
        </a>
        <a href="{{ route('technician.assigned') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('technician.assigned') ? 'text-blue-500' : '' }}">
            <i class="fas fa-clipboard-list text-xl"></i>
            <span class="text-xs mt-1">Assigned</span>
        </a>
        <a href="{{ route('technician.started') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('technician.started') ? 'text-blue-500' : '' }}">
            <i class="fas fa-tools text-xl"></i>
            <span class="text-xs mt-1">Started</span>
        </a>
        <a href="{{ route('technician.completed') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-500 {{ request()->routeIs('technician.completed') ? 'text-blue-500' : '' }}">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="text-xs mt-1">Completed</span>
        </a>
    @endif
</nav>

<!-- Mobile Profile Menu -->
<div x-data="{ open: false }" class="md:hidden fixed top-0 right-0 p-4 z-50">
    <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-900">
        <i class="fas fa-user-circle text-2xl"></i>
    </button>
    
    <div x-show="open" 
         @click.away="open = false"
         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
        <div class="py-1">
            <div class="px-4 py-2 text-sm text-gray-700">
                {{ auth()->user()->name }}
            </div>
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div> 