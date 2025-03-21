@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Admin Dashboard')

@section('content')
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
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Maintenance Requests</h2>
        
        @if($recentRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentRequests as $request)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ Str::limit($request->title, 30) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->property->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status == 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($request->status == 'approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Approved
                                        </span>
                                    @elseif($request->status == 'in_progress')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            In Progress
                                        </span>
                                    @elseif($request->status == 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @elseif($request->status == 'declined')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Declined
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('maintenance.show', $request) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="{{ route('maintenance.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View All Requests</a>
            </div>
        @else
            <p class="text-gray-500">No maintenance requests found.</p>
        @endif
    </div>
    
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
        
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users.create') }}" class="block px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm font-medium text-gray-700">
                    <i class="fas fa-user-plus mr-2 text-blue-500"></i> Add New User
                </a>
                <a href="{{ route('properties.create') }}" class="block px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm font-medium text-gray-700">
                    <i class="fas fa-building mr-2 text-purple-500"></i> Add New Property
                </a>
                <a href="{{ route('maintenance.create') }}" class="block px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm font-medium text-gray-700">
                    <i class="fas fa-tools mr-2 text-yellow-500"></i> Create Maintenance Request
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 