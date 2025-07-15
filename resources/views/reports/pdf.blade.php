<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Report - PDF</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            /* Hide everything non-essential for PDF */
            .no-print {
                display: none !important;
            }
            
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                font-size: 11px;
                line-height: 1.3;
                color: #000 !important;
                background: white !important;
            }
            
            .pdf-container {
                width: 100% !important;
                margin: 0 !important;
                padding: 15px !important;
                max-width: none !important;
                background: white !important;
            }
            
            /* Clean up styling for print */
            .bg-white, .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50 {
                background: white !important;
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            .rounded-lg, .rounded-xl {
                border-radius: 0 !important;
            }
            
            .shadow, .shadow-lg {
                box-shadow: none !important;
            }
            
            /* Table styling for print */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                page-break-inside: avoid;
            }
            
            th, td {
                border: 1px solid #ddd !important;
                padding: 4px 6px !important;
                font-size: 10px !important;
            }
            
            /* Grid adjustments */
            .grid {
                display: block !important;
            }
            
            .grid > div {
                display: block !important;
                margin-bottom: 8px !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            
            /* Typography for print */
            h1 {
                font-size: 16px !important;
                margin-bottom: 8px !important;
            }
            
            h2, h3 {
                font-size: 14px !important;
                margin-bottom: 6px !important;
            }
            
            p {
                margin-bottom: 4px !important;
            }
        }
        
        @media screen {
            body {
                background: #f3f4f6;
                padding: 20px;
            }
            
            .pdf-container {
                max-width: 8.5in;
                margin: 0 auto;
                background: white;
                padding: 40px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <!-- Header -->
        <div class="border-b-2 border-gray-300 pb-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Maintenance Report</h1>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p><strong>Report Type:</strong> {{ $report_type }}</p>
                    <p><strong>Date Range:</strong> {{ $dateRange['label'] }}</p>
                </div>
                <div class="text-right">
                    <p><strong>Generated:</strong> {{ now()->format('M d, Y H:i') }}</p>
                    <p><strong>Total Requests:</strong> {{ $summary['total_requests'] }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center border">
                <div class="text-2xl font-bold text-blue-600">{{ $summary['total_requests'] }}</div>
                <div class="text-sm text-blue-800">Total Requests</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center border">
                <div class="text-2xl font-bold text-green-600">{{ $summary['completed_requests'] }}</div>
                <div class="text-sm text-green-800">Completed</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center border">
                <div class="text-2xl font-bold text-yellow-600">{{ $summary['pending_requests'] }}</div>
                <div class="text-sm text-yellow-800">Pending</div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center border">
                <div class="text-2xl font-bold text-purple-600">{{ $summary['completion_rate'] }}%</div>
                <div class="text-sm text-purple-800">Completion Rate</div>
            </div>
        </div>

        <!-- Status Breakdown -->
        @if(isset($breakdowns['status']) && count($breakdowns['status']) > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-1">Status Breakdown</h2>
            <div class="grid grid-cols-2 gap-2">
                @foreach($breakdowns['status'] as $status => $count)
                <div class="flex justify-between border-b border-gray-100 py-1">
                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    <span>{{ $count }} ({{ $summary['total_requests'] > 0 ? round(($count / $summary['total_requests']) * 100, 1) : 0 }}%)</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Priority Breakdown -->
        @if(isset($breakdowns['priority']) && count($breakdowns['priority']) > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-1">Priority Breakdown</h2>
            <div class="grid grid-cols-2 gap-2">
                @foreach($breakdowns['priority'] as $priority => $count)
                <div class="flex justify-between border-b border-gray-100 py-1">
                    <span class="font-medium">{{ ucfirst($priority) }} Priority</span>
                    <span>{{ $count }} ({{ $summary['total_requests'] > 0 ? round(($count / $summary['total_requests']) * 100, 1) : 0 }}%)</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Property Performance -->
        @if(isset($breakdowns['property']) && count($breakdowns['property']) > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-1">Property Performance</h2>
            @foreach($breakdowns['property'] as $propertyName => $data)
            <div class="flex justify-between border-b border-gray-100 py-2">
                <div>
                    <span class="font-medium">{{ $propertyName }}</span><br>
                    <span class="text-xs text-gray-600">Total: {{ $data['count'] }}</span>
                </div>
                <div class="text-right">
                    <span class="font-medium">{{ $data['count'] > 0 ? round(($data['completed'] / $data['count']) * 100, 1) : 0 }}%</span><br>
                    <span class="text-xs text-gray-600">Completed: {{ $data['completed'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Requests Table -->
        @if(count($requests) > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-1">Request Details</h2>
            <table class="w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">ID</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">Title</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">Property</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">Priority</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">Technician</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">Created</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border">Completed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td class="px-3 py-2 text-sm text-gray-900 border">{{ $request->id }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900 border">{{ $request->title }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900 border">{{ $request->property->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2 text-sm border">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($request->priority === 'high') bg-red-100 text-red-800
                                @elseif($request->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($request->priority) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-sm border">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($request->status === 'completed') bg-green-100 text-green-800
                                @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($request->status === 'started') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-900 border">
                            {{ $request->assignedTechnician->name ?? 'Not Assigned' }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-500 border">
                            {{ $request->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-500 border">
                            {{ $request->completed_at ? $request->completed_at->format('M d, Y') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500">No requests found for the selected criteria.</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="border-t-2 border-gray-300 pt-4 mt-8 text-center text-xs text-gray-500">
            <p>This report was generated automatically by MaintainXtra on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>

    <!-- Print Controls (only visible on screen) -->
    <div class="no-print fixed bottom-4 right-4 space-x-2">
        <button onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-lg">
            🖨️ Print PDF
        </button>
        <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-lg">
            ✕ Close
        </button>
    </div>

    <script>
        // Auto-trigger print dialog when page loads
        window.addEventListener('load', function() {
            // Small delay to ensure page is fully rendered
            setTimeout(function() {
                window.print();
            }, 500);
        });
        
        // Close window after printing (if opened in new tab)
        window.addEventListener('afterprint', function() {
            // Give user a moment to see the result
            setTimeout(function() {
                if (window.opener) {
                    window.close();
                }
            }, 1000);
        });
    </script>
</body>
</html> 