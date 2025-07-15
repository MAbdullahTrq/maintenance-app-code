@extends('mobile.layout')

@section('title', 'Report Results')

@section('header-actions')
<a href="{{ route('mobile.reports.create') }}" class="text-sm font-medium">New Report</a>
@endsection

@section('content')
<div class="flex justify-center px-4">
    <div class="bg-white rounded-xl shadow p-4 w-full max-w-6xl mx-auto print-content">
        <!-- Report Header -->
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900 mb-1">📊 Maintenance Report</h1>
            <p class="text-sm text-gray-600">{{ $report_type }}</p>
            <p class="text-xs text-gray-500">{{ $dateRange['label'] }}</p>
        </div>

        <!-- Export Actions -->
        <div class="mb-6 flex space-x-2 no-print">
            <button onclick="window.print()" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-sm font-medium">
                🖨️ Print Report
            </button>
            <form method="POST" action="{{ route('mobile.reports.generate') }}" class="inline flex-1">
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
                <button type="submit" name="format" value="csv" class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium">
                    📥 Export CSV
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $summary['total_requests'] }}</div>
                <div class="text-xs text-blue-800">Total Requests</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $summary['completed_requests'] }}</div>
                <div class="text-xs text-green-800">Completed</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $summary['pending_requests'] }}</div>
                <div class="text-xs text-yellow-800">Pending</div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $summary['completion_rate'] }}%</div>
                <div class="text-xs text-purple-800">Completion Rate</div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">📈 Status Breakdown</h3>
            <div class="space-y-2">
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
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded {{ $colorClass }} mr-3"></div>
                            <span class="text-sm font-medium">{{ ucfirst($status) }}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $count }} <span class="text-xs">({{ $percentage }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Priority Breakdown -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">⚡ Priority Breakdown</h3>
            <div class="space-y-2">
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
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded {{ $colorClass }} mr-3"></div>
                            <span class="text-sm font-medium">{{ ucfirst($priority) }} Priority</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $count }} <span class="text-xs">({{ $percentage }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Performance Metrics -->
        @if($summary['average_completion_time'])
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">⏱️ Performance Metrics</h3>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div>
                    <div class="text-xl font-bold text-blue-600">{{ $summary['average_completion_time'] }}</div>
                    <div class="text-xs text-blue-800">Avg. Hours</div>
                </div>
                <div>
                    <div class="text-xl font-bold text-green-600">{{ $summary['completion_rate'] }}%</div>
                    <div class="text-xs text-green-800">Success Rate</div>
                </div>
                <div>
                    <div class="text-xl font-bold text-purple-600">{{ count($breakdowns['technician']) }}</div>
                    <div class="text-xs text-purple-800">Technicians</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Property Performance -->
        @if($breakdowns['property']->isNotEmpty())
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">🏢 Property Performance</h3>
            <div class="space-y-3">
                @foreach($breakdowns['property'] as $propertyName => $data)
                    @php
                        $completionRate = $data['count'] > 0 ? round(($data['completed'] / $data['count']) * 100, 1) : 0;
                    @endphp
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div class="text-sm font-medium text-gray-900">{{ $propertyName }}</div>
                            <div class="text-xs text-gray-500">{{ $completionRate }}%</div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 mb-2">
                            <span>Total: {{ $data['count'] }}</span>
                            <span>Completed: {{ $data['completed'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Technician Performance -->
        @if($breakdowns['technician']->isNotEmpty())
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">🔧 Technician Performance</h3>
            <div class="space-y-3">
                @foreach($breakdowns['technician'] as $technicianName => $data)
                    @php
                        $completionRate = $data['count'] > 0 ? round(($data['completed'] / $data['count']) * 100, 1) : 0;
                    @endphp
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div class="text-sm font-medium text-gray-900">{{ $technicianName }}</div>
                            <div class="text-xs text-gray-500">{{ $completionRate }}%</div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Tasks: {{ $data['count'] }}</span>
                            <span>Done: {{ $data['completed'] }}</span>
                        </div>
                        @if($data['avg_completion_time'])
                        <div class="text-xs text-gray-500 mb-2">
                            Avg. time: {{ number_format($data['avg_completion_time'], 1) }} hours
                        </div>
                        @endif
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Detailed Request List -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">📋 Request Details</h3>
            <p class="text-sm text-gray-500 mb-3">{{ $requests->count() }} requests found</p>
            
            @if($requests->isNotEmpty())
                <div class="space-y-3">
                    @foreach($requests as $request)
                        <div class="p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 border hover:border-blue-300 transition-colors" onclick="window.open('{{ route('mobile.request.show', $request->id) }}', '_blank')">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ $request->title }}</div>
                                <div class="flex space-x-1">
                                    <span class="px-2 py-1 text-xs rounded 
                                        @if($request->priority == 'low') bg-green-100 text-green-800
                                        @elseif($request->priority == 'medium') bg-yellow-100 text-yellow-800
                                        @elseif($request->priority == 'high') bg-red-100 text-red-800
                                        @endif">
                                        {{ strtoupper($request->priority) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-xs text-gray-600 space-y-1">
                                <div>🏢 {{ $request->property->name ?? 'N/A' }}</div>
                                <div>👤 {{ $request->property->owner->name ?? 'N/A' }}</div>
                                <div>🔧 {{ $request->assignedTechnician->name ?? 'Not Assigned' }}</div>
                                <div class="flex justify-between">
                                    <span>📅 {{ $request->created_at->format('M d, Y') }}</span>
                                    <span class="px-2 py-1 text-xs rounded 
                                        @if($request->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($request->status == 'assigned') bg-blue-100 text-blue-800
                                        @elseif($request->status == 'started') bg-purple-100 text-purple-800
                                        @elseif($request->status == 'completed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                                @if($request->completed_at)
                                <div>✅ Completed: {{ $request->completed_at->format('M d, Y') }}</div>
                                @endif
                                <div class="mt-2 text-blue-500 text-xs">
                                    👆 Tap to open request details
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-gray-500">
                    <div class="text-4xl mb-2">📭</div>
                    <p>No requests found for the selected criteria.</p>
                </div>
            @endif
        </div>

        <!-- Back to Create -->
        <div class="text-center">
            <a href="{{ route('mobile.reports.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                📊 Create New Report
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Hide everything except report content */
        * {
            visibility: hidden;
        }
        
        /* Show only the main report container and its children */
        .print-content, .print-content * {
            visibility: visible;
        }
        
        /* Hide specific elements */
        .no-print {
            display: none !important;
        }
        
        /* Reset page layout for printing */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            font-size: 10px;
            line-height: 1.3;
            color: #000 !important;
            background: white !important;
            height: auto !important;
            overflow: visible !important;
        }
        
        /* Position report content to fill page */
        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 15px !important;
            max-width: none !important;
            background: white !important;
        }
        
        /* Clean up styling for print */
        .bg-white, .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50, .bg-purple-50 {
            background: white !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        
        .rounded-xl, .rounded-lg {
            border-radius: 0 !important;
        }
        
        .shadow {
            box-shadow: none !important;
        }
        
        /* Grid adjustments for mobile */
        .grid {
            display: block !important;
        }
        
        .grid > div {
            display: block !important;
            margin-bottom: 6px !important;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        
        /* Typography for print */
        h1 {
            font-size: 14px !important;
            margin-bottom: 6px !important;
        }
        
        h2, h3 {
            font-size: 12px !important;
            margin-bottom: 4px !important;
        }
        
        p {
            margin-bottom: 3px !important;
        }
        
        /* Mobile-specific print adjustments */
        .space-y-3 > * + * {
            margin-top: 6px !important;
        }
        
        .mb-6 {
            margin-bottom: 12px !important;
        }
        
        .p-4, .p-3 {
            padding: 6px !important;
        }
        
        .text-2xl {
            font-size: 14px !important;
        }
        
        .text-xl {
            font-size: 12px !important;
        }
        
        .text-sm, .text-xs {
            font-size: 9px !important;
        }
        
        .cursor-pointer {
            cursor: default !important;
        }
        
        .hover\:bg-gray-100:hover,
        .hover\:border-blue-300:hover {
            background-color: transparent !important;
            border-color: transparent !important;
        }
        
        .transition-colors {
            transition: none !important;
        }
        
        /* Hide tap instruction when printing */
        .text-blue-500:last-child {
            display: none !important;
        }
    }
</style>
@endsection 