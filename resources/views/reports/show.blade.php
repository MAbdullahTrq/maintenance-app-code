@extends('layouts.app')

@section('title', 'Maintenance Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Report Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Maintenance Report</h1>
            <p class="text-gray-600 mt-1">{{ $report_type }} • {{ $dateRange['label'] }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('reports.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-plus mr-2"></i>New Report
            </a>
            <form method="POST" action="{{ route('reports.generate') }}" class="inline">
                @csrf
                @foreach($filters as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <button type="submit" name="format" value="csv" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clipboard-list text-2xl text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['total_requests'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-green-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['completed_requests'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl text-yellow-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['pending_requests'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-percentage text-2xl text-purple-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['completion_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Status Breakdown -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Breakdown</h3>
            <div class="space-y-3">
                @foreach($breakdowns['status'] as $status => $count)
                    @php
                        $percentage = $summary['total_requests'] > 0 ? round(($count / $summary['total_requests']) * 100, 1) : 0;
                        $colorClass = match($status) {
                            'completed' => 'bg-green-500',
                            'pending' => 'bg-yellow-500',
                            'assigned' => 'bg-blue-500',
                            'started' => 'bg-purple-500',
                            'declined' => 'bg-red-500',
                            default => 'bg-gray-500'
                        };
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded {{ $colorClass }} mr-3"></div>
                            <span class="text-sm font-medium">{{ ucfirst($status) }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">{{ $count }}</span>
                            <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Priority Breakdown -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Priority Breakdown</h3>
            <div class="space-y-3">
                @foreach($breakdowns['priority'] as $priority => $count)
                    @php
                        $percentage = $summary['total_requests'] > 0 ? round(($count / $summary['total_requests']) * 100, 1) : 0;
                        $colorClass = match($priority) {
                            'high' => 'bg-red-500',
                            'medium' => 'bg-yellow-500',
                            'low' => 'bg-green-500',
                            default => 'bg-gray-500'
                        };
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded {{ $colorClass }} mr-3"></div>
                            <span class="text-sm font-medium">{{ ucfirst($priority) }} Priority</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">{{ $count }}</span>
                            <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    @if($summary['average_completion_time'])
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $summary['average_completion_time'] }}</div>
                <div class="text-sm text-gray-500">Average Completion Time (Hours)</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ $summary['completion_rate'] }}%</div>
                <div class="text-sm text-gray-500">Completion Rate</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ count($breakdowns['technician']) }}</div>
                <div class="text-sm text-gray-500">Active Technicians</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Property Performance -->
    @if($breakdowns['property']->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Property Performance</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Requests</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($breakdowns['property'] as $propertyName => $data)
                        @php
                            $completionRate = $data['count'] > 0 ? round(($data['completed'] / $data['count']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $propertyName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['completed'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $completionRate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Technician Performance -->
    @if($breakdowns['technician']->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Technician Performance</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Requests</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Time (Hours)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($breakdowns['technician'] as $technicianName => $data)
                        @php
                            $completionRate = $data['count'] > 0 ? round(($data['completed'] / $data['count']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $technicianName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['completed'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $completionRate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $data['avg_completion_time'] ? number_format($data['avg_completion_time'], 1) : 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Detailed Request List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detailed Request List</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $requests->count() }} requests found</p>
        </div>
        
        @if($requests->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->title }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $request->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->property->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->property->owner->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($request->priority == 'low') bg-green-100 text-green-800
                                        @elseif($request->priority == 'medium') bg-yellow-100 text-yellow-800
                                        @elseif($request->priority == 'high') bg-red-100 text-red-800
                                        @endif">
                                        {{ strtoupper($request->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($request->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($request->status == 'assigned') bg-blue-100 text-blue-800
                                        @elseif($request->status == 'started') bg-purple-100 text-purple-800
                                        @elseif($request->status == 'completed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->assignedTechnician->name ?? 'Not Assigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->completed_at ? $request->completed_at->format('M d, Y H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-500">No requests found for the selected criteria.</p>
            </div>
        @endif
    </div>
</div>
@endsection 