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
                font-size: 12px;
                line-height: 1.4;
                color: #000 !important;
                background: white !important;
            }
            
            .pdf-container {
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
                max-width: none !important;
                background: white !important;
            }
            
            /* Clean up styling for print */
            .bg-white, .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50, .bg-gray-50 {
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
                margin-top: 15px !important;
            }
            
            th, td {
                border: 1px solid #333 !important;
                padding: 6px 8px !important;
                font-size: 11px !important;
                text-align: left !important;
            }
            
            th {
                background: #f5f5f5 !important;
                font-weight: bold !important;
            }
            
            /* Typography for print */
            h1 {
                font-size: 20px !important;
                margin-bottom: 10px !important;
                font-weight: bold !important;
            }
            
            h2 {
                font-size: 16px !important;
                margin-bottom: 8px !important;
                font-weight: bold !important;
            }
            
            h3 {
                font-size: 14px !important;
                margin-bottom: 6px !important;
                font-weight: bold !important;
            }
            
            p {
                margin-bottom: 6px !important;
            }
            
            .ai-summary {
                background: #f8f9fa !important;
                border: 1px solid #ddd !important;
                padding: 15px !important;
                margin: 15px 0 !important;
                page-break-inside: avoid;
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
        <!-- Report Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                📊 Report{{ isset($owner_name) && $owner_name ? ' for ' . $owner_name : '' }}
            </h1>
            
            @if((isset($property_names) && count($property_names) > 0) || (isset($technician_names) && count($technician_names) > 0))
                <div class="text-sm text-gray-600 mb-3 space-y-1">
                    @if(isset($property_names) && count($property_names) > 0)
                        <div>
                            <span class="font-medium">Properties:</span> 
                            {{ implode(', ', $property_names) }}
                        </div>
                    @endif
                    @if(isset($technician_names) && count($technician_names) > 0)
                        <div>
                            <span class="font-medium">Technicians:</span> 
                            {{ implode(', ', $technician_names) }}
                        </div>
                    @endif
                </div>
            @endif
            
            <p class="text-sm text-gray-500 mb-1">{{ $dateRange['label'] }}</p>
            <p class="text-xs text-gray-400">Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>

        <!-- AI Summary Section -->
        @if(isset($ai_summary) && $ai_summary)
        <div class="ai-summary mb-6">
            <h2 class="text-lg font-bold text-blue-800 mb-3">🧠 AI Summary</h2>
            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $ai_summary }}</div>
        </div>
        @else
        <div class="ai-summary mb-6">
            <h2 class="text-lg font-bold text-blue-800 mb-3">🧠 AI Summary</h2>
            <div class="text-sm text-gray-700 leading-relaxed">
                <p><strong>Maintenance Report Summary:</strong></p>
                <p>- Total maintenance requests: {{ $summary['total_requests'] }}</p>
                <p>- Completed tasks: {{ $summary['completed_requests'] }}</p>
                <p>- Pending tasks: {{ $summary['pending_requests'] }}</p>
                <p>- Average completion time: {{ $summary['average_completion_time'] ? $summary['average_completion_time'] . ' hours' : 'N/A' }}</p>
                
                @if(isset($breakdowns['status']) && count($breakdowns['status']) > 0)
                <p style="margin-top: 10px;"><strong>Status Breakdown:</strong></p>
                @foreach($breakdowns['status'] as $status => $count)
                <p>- {{ ucfirst($status) }}: {{ $count }}</p>
                @endforeach
                @endif
                
                @if(isset($breakdowns['priority']) && count($breakdowns['priority']) > 0)
                <p style="margin-top: 10px;"><strong>Priority Breakdown:</strong></p>
                @foreach($breakdowns['priority'] as $priority => $count)
                <p>- {{ ucfirst($priority) }} Priority: {{ $count }}</p>
                @endforeach
                @endif
            </div>
        </div>
        @endif

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-5 gap-0 mb-6 border border-gray-400 rounded overflow-hidden">
            <div class="text-center p-4 border-r border-gray-400 bg-gray-50">
                <div class="font-semibold text-sm">Declined</div>
                <div class="text-lg font-bold">{{ $requests->where('status', 'declined')->count() }}</div>
            </div>
            <div class="text-center p-4 border-r border-gray-400 bg-gray-50">
                <div class="font-semibold text-sm">Assigned</div>
                <div class="text-lg font-bold">{{ $requests->where('status', 'assigned')->count() }}</div>
            </div>
            <div class="text-center p-4 border-r border-gray-400 bg-gray-50">
                <div class="font-semibold text-sm">Accepted</div>
                <div class="text-lg font-bold">{{ $requests->where('status', 'accepted')->count() }}</div>
            </div>
            <div class="text-center p-4 border-r border-gray-400 bg-gray-50">
                <div class="font-semibold text-sm">Started</div>
                <div class="text-lg font-bold">{{ $requests->where('status', 'started')->count() }}</div>
            </div>
            <div class="text-center p-4 bg-gray-50">
                <div class="font-semibold text-sm">Completed</div>
                <div class="text-lg font-bold">{{ $requests->where('status', 'completed')->count() }}</div>
            </div>
        </div>

        <!-- Main Report Content -->
        @if($requests->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Maintenance Requests</h3>
                <div class="overflow-x-auto w-full">
                    <table class="min-w-full text-sm border border-gray-400 border-collapse rounded overflow-hidden">
                        <colgroup>
                            <col class="w-2/5">
                            <col class="w-1/6">
                            <col class="w-1/6">
                            <col class="w-1/6">
                        </colgroup>
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-400">
                                <th class="p-4 border-r border-gray-400 text-left">Property</th>
                                <th class="p-4 border-r border-gray-400 text-center">Priority</th>
                                <th class="p-4 border-r border-gray-400 text-center">Date</th>
                                <th class="p-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr class="border-b border-gray-400 hover:bg-gray-50">
                                <td class="p-4 align-top border-r border-gray-400">
                                    <span class="font-semibold">{{ $request->property->name ?? 'N/A' }}</span><br>
                                    <span class="text-gray-500 text-sm">{{ $request->property->address ?? 'No address' }}</span>
                                </td>
                                <td class="p-4 align-top border-r border-gray-400 text-center {{ $request->priority == 'high' ? 'bg-red-500 text-white' : ($request->priority == 'low' ? 'bg-yellow-200' : ($request->priority == 'medium' ? 'bg-yellow-100' : '')) }}">
                                    {{ ucfirst($request->priority) }}
                                </td>
                                <td class="p-4 align-top border-r border-gray-400 text-center">
                                    <div>{{ $request->created_at->format('d M, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->created_at->format('H:i') }}</div>
                                </td>
                                <td class="p-4 align-top text-center">{{ ucfirst($request->status) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-4xl mb-3">📭</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No requests found</h3>
                <p class="text-sm text-gray-600">No maintenance requests match the selected criteria.</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="border-t-2 border-gray-300 pt-4 mt-8 text-center text-xs text-gray-500">
            <p>This report was generated automatically by MaintainXtra</p>
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
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html> 