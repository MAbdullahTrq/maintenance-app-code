@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Admin Dashboard</h1>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                    <i class="fas fa-user-tie text-blue-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Property Managers</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalPropertyManagers }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                    <i class="fas fa-hard-hat text-green-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Technicians</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalTechnicians }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                    <i class="fas fa-building text-purple-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Properties</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalProperties }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                    <i class="fas fa-tools text-yellow-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalRequests }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Active Users Table -->
        <div class="bg-white rounded-lg shadow p-4 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-users mr-2 text-blue-500"></i>
                <span>Active Users</span>
            </h2>
            
            @if($activeUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($activeUsers as $user)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($user->role->slug == 'admin')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Admin
                                            </span>
                                        @elseif($user->role->slug == 'property_manager')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Property Manager
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Technician
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            
                                            @if($user->role->slug === 'property_manager')
                                                <a href="{{ route('admin.users.grant-subscription.create', $user) }}" class="text-green-600 hover:text-green-900">Grant Subscription</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-900">
                        View All Users
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @else
                <p class="text-gray-500">No active users found.</p>
            @endif
        </div>
        
        <!-- Sidebar Sections -->
        <div class="space-y-6">
            <!-- Request Status -->
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-indigo-500"></i>
                    <span>Request Status</span>
                </h2>
                
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Pending</span>
                            <span class="text-sm font-medium text-gray-700">{{ $pendingRequests }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalRequests > 0 ? ($pendingRequests / $totalRequests * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">In Progress</span>
                            <span class="text-sm font-medium text-gray-700">{{ $inProgressRequests }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalRequests > 0 ? ($inProgressRequests / $totalRequests * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Completed</span>
                            <span class="text-sm font-medium text-gray-700">{{ $completedRequests }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalRequests > 0 ? ($completedRequests / $totalRequests * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Closed</span>
                            <span class="text-sm font-medium text-gray-700">{{ $closedRequests }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-500 h-2 rounded-full" style="width: {{ $totalRequests > 0 ? ($closedRequests / $totalRequests * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Management -->
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-crown mr-2 text-yellow-500"></i>
                    <span>Subscription Management</span>
                </h2>
                
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Active Subscriptions</span>
                            <span class="text-sm font-medium text-gray-700">{{ $activeSubscriptions ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ isset($totalPropertyManagers) && $totalPropertyManagers > 0 ? (($activeSubscriptions ?? 0) / $totalPropertyManagers * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Expired Subscriptions</span>
                            <span class="text-sm font-medium text-gray-700">{{ $expiredSubscriptions ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ isset($totalPropertyManagers) && $totalPropertyManagers > 0 ? (($expiredSubscriptions ?? 0) / $totalPropertyManagers * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded text-sm font-medium text-gray-700 transition">
                            <i class="fas fa-users mr-2 text-blue-500"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.subscription.plans.index') }}" class="block px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded text-sm font-medium text-gray-700 transition">
                            <i class="fas fa-tags mr-2 text-yellow-500"></i> Manage Plans
                        </a>
                        <a href="{{ route('admin.subscription.index') }}" class="block px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded text-sm font-medium text-gray-700 transition">
                            <i class="fas fa-list-alt mr-2 text-purple-500"></i> View All Subscriptions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Add !important to critical CSS classes to prevent overrides */
.admin-dashboard-container h1,
.admin-dashboard-container h2,
.admin-dashboard-container h3 {
  font-family: sans-serif !important;
  margin-bottom: 0.5rem !important;
}

.admin-dashboard-container table {
  width: 100% !important;
  border-collapse: collapse !important;
}

.admin-dashboard-container th,
.admin-dashboard-container td {
  padding: 0.5rem !important;
  text-align: left !important;
}

.admin-dashboard-container .bg-white {
  background-color: white !important;
}

.admin-dashboard-container .rounded-lg {
  border-radius: 0.5rem !important;
}

.admin-dashboard-container .shadow {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Add admin-dashboard-container class to the main container for scoped styling
  document.querySelector('.container').classList.add('admin-dashboard-container');
  
  // Force re-render of critical elements
  const tables = document.querySelectorAll('table');
  tables.forEach(function(table) {
    table.style.display = 'table';
    table.style.width = '100%';
  });
  
  // Force FontAwesome icons to display correctly
  const icons = document.querySelectorAll('.fas');
  icons.forEach(function(icon) {
    icon.style.display = 'inline-block';
    icon.style.verticalAlign = 'middle';
  });
});
</script>
@endsection 