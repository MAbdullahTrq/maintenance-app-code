@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 pt-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
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
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                    <i class="fas fa-user-hard-hat text-green-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Technicians</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalTechnicians }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
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
        
        <div class="bg-white rounded-lg shadow p-6">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-users mr-2 text-blue-500"></i>Active Users
            </h2>
            
            @if($activeUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($activeUsers as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->role->slug == 'admin')
                                            <span class="px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Admin
                                            </span>
                                        @elseif($user->role->slug == 'property_manager')
                                            <span class="px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $user->role->name }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $user->role->name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View All Users</a>
                </div>
            @else
                <p class="text-gray-500">No active users found.</p>
            @endif
        </div>
        
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Request Status</h2>
                
                <div class="space-y-4">
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
                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $totalRequests > 0 ? ($inProgressRequests / $totalRequests * 100) : 0 }}%"></div>
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
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-crown mr-2 text-yellow-500"></i>Subscription Management
                </h2>
                
                <div class="space-y-4">
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

                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.users.create') }}" class="block px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm font-medium text-gray-700">
                            <i class="fas fa-user-plus mr-2 text-blue-500"></i> Add New User
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm font-medium text-gray-700">
                            <i class="fas fa-users mr-2 text-green-500"></i> View Users
                        </a>
                        <a href="{{ route('admin.subscription.plans') }}" class="block px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm font-medium text-gray-700">
                            <i class="fas fa-tags mr-2 text-yellow-500"></i> Manage Plans
                        </a>
                        <a href="{{ route('admin.subscription.index') }}" class="block px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm font-medium text-gray-700">
                            <i class="fas fa-list-alt mr-2 text-purple-500"></i> View All Subscriptions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 