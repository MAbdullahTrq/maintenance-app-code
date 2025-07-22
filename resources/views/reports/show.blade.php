@extends('layouts.app')

@section('title', 'Maintenance Report')

@section('content')
<div class="container mx-auto px-4 py-8 print-content">
    <!-- Report Header with Export Controls -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Report</h1>
                <p class="text-gray-600 mt-1">{{ $report_type }} • {{ $dateRange['label'] }}</p>
            </div>
            <div class="flex space-x-3 no-print">
                <!-- Export Dropdown -->
                <div class="relative">
                    <button id="exportDropdownBtn" type="button" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center">
                        Export
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="exportDropdown" class="hidden absolute top-full right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10 min-w-48">
                        <button onclick="exportReport('csv')" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700 border-b border-gray-100">
                            📊 CSV Spreadsheet
                        </button>
                        <button onclick="exportReport('pdf')" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700 border-b border-gray-100">
                            📄 PDF Document
                        </button>
                        <button onclick="exportReport('docx')" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700 border-b border-gray-100">
                            📝 Word Document
                        </button>
                        <button onclick="printReport()" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700">
                            🖨️ Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Generate AI Summary Button -->
        <div class="text-center no-print">
            <button id="generateAISummary" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                Generate AI summary
            </button>
        </div>
    </div>

    <!-- AI Summary Section -->
    <div id="aiSummarySection" class="mb-6 no-print" style="display: none;">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-brain text-blue-600 text-xl mr-3"></i>
                <h2 class="text-xl font-bold text-blue-800">AI-Generated Summary</h2>
            </div>
            <div id="aiSummaryContent" class="text-gray-700 leading-relaxed whitespace-pre-line"></div>
            <div id="aiSummaryLoading" class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-blue-600">Generating AI summary...</span>
            </div>
            <div id="aiSummaryError" class="text-red-600" style="display: none;"></div>
        </div>
    </div>

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

    <!-- Main Report Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($requests->isNotEmpty())
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-sm border border-gray-400 border-collapse rounded overflow-hidden">
                    <colgroup>
                        <col class="w-2/5">
                        <col class="w-1/6">
                        <col class="w-1/6">
                        <col class="w-1/6">
                        <col class="w-1/12">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-4 border-r border-gray-400 text-left">Property</th>
                            <th class="p-4 border-r border-gray-400 text-center">Priority</th>
                            <th class="p-4 border-r border-gray-400 text-center">Date</th>
                            <th class="p-4 border-r border-gray-400 text-center">Status</th>
                            <th class="p-4 text-center">Actions</th>
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
                                <td class="p-4 align-top border-r border-gray-400 text-center">{{ ucfirst($request->status) }}</td>
                                <td class="p-4 align-top text-center">
                                    <a href="{{ route('maintenance.show', $request) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 text-lg">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="text-gray-400 text-4xl mb-4">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No requests found</h3>
                <p class="text-gray-500">No maintenance requests match the selected criteria.</p>
                <div class="mt-6">
                    <a href="{{ route('reports.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Create New Report
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if($requests->isNotEmpty())
    <!-- Back to Create Button -->
    <div class="text-center mt-6 no-print">
        <a href="{{ route('reports.create') }}" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Create New Report
        </a>
    </div>
    @endif
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-content {
            margin: 0;
            padding: 15px;
        }
        
        table {
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        .bg-white {
            background: white !important;
            box-shadow: none !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generateAISummary');
    const summarySection = document.getElementById('aiSummarySection');
    const summaryContent = document.getElementById('aiSummaryContent');
    const summaryLoading = document.getElementById('aiSummaryLoading');
    const summaryError = document.getElementById('aiSummaryError');

    // Export dropdown functionality
    const exportDropdownBtn = document.getElementById('exportDropdownBtn');
    const exportDropdown = document.getElementById('exportDropdown');

    exportDropdownBtn.addEventListener('click', function() {
        exportDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!exportDropdownBtn.contains(event.target) && !exportDropdown.contains(event.target)) {
            exportDropdown.classList.add('hidden');
        }
    });

    generateBtn.addEventListener('click', function() {
        summarySection.style.display = 'block';
        summaryContent.style.display = 'none';
        summaryLoading.style.display = 'flex';
        summaryError.style.display = 'none';
        
        generateBtn.disabled = true;
        generateBtn.innerHTML = 'Generating...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        
        @foreach($filters as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)
                    formData.append('{{ $key }}[]', '{{ $item }}');
                @endforeach
            @else
                formData.append('{{ $key }}', '{{ $value }}');
            @endif
        @endforeach

        fetch('{{ route("reports.ai-summary") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            summaryLoading.style.display = 'none';
            
            if (data.success) {
                summaryContent.textContent = data.summary;
                summaryContent.style.display = 'block';
            } else {
                summaryError.textContent = data.error || 'Failed to generate AI summary.';
                summaryError.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            summaryLoading.style.display = 'none';
            summaryError.textContent = 'Network error. Please try again.';
            summaryError.style.display = 'block';
        })
        .finally(() => {
            generateBtn.disabled = false;
            generateBtn.innerHTML = 'Generate AI summary';
        });
    });
});

// Export report function
function exportReport(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    
    if (format === 'csv') {
        form.action = '{{ route("reports.csv") }}';
    } else if (format === 'pdf') {
        form.action = '{{ route("reports.pdf") }}';
        form.target = '_blank';
    } else if (format === 'docx') {
        form.action = '{{ route("reports.docx") }}';
    }
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add all filter data
    @foreach($filters as $key => $value)
        @if(is_array($value))
            @foreach($value as $item)
                const input{{ $loop->parent->index }}_{{ $loop->index }} = document.createElement('input');
                input{{ $loop->parent->index }}_{{ $loop->index }}.type = 'hidden';
                input{{ $loop->parent->index }}_{{ $loop->index }}.name = '{{ $key }}[]';
                input{{ $loop->parent->index }}_{{ $loop->index }}.value = '{{ $item }}';
                form.appendChild(input{{ $loop->parent->index }}_{{ $loop->index }});
            @endforeach
        @else
            const input{{ $loop->index }} = document.createElement('input');
            input{{ $loop->index }}.type = 'hidden';
            input{{ $loop->index }}.name = '{{ $key }}';
            input{{ $loop->index }}.value = '{{ $value }}';
            form.appendChild(input{{ $loop->index }});
        @endif
    @endforeach
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Close dropdown
    document.getElementById('exportDropdown').classList.add('hidden');
}

// Print report function
function printReport() {
    // Always generate AI summary for print
    const summarySection = document.getElementById('aiSummarySection');
    const summaryContent = document.getElementById('aiSummaryContent');
    
    // Show summary section first
    summarySection.style.display = 'block';
    
    // If summary already exists, print immediately
    if (summaryContent.style.display === 'block' && summaryContent.textContent.trim()) {
        setTimeout(() => window.print(), 100);
    } else {
        // Generate new summary
        document.getElementById('generateAISummary').click();
        
        // Wait for AI summary to load before printing with timeout
        let attempts = 0;
        const maxAttempts = 20; // 10 seconds max wait
        
        const checkSummary = setInterval(function() {
            attempts++;
            const summaryContent = document.getElementById('aiSummaryContent');
            
            if (summaryContent.style.display === 'block' && summaryContent.textContent.trim()) {
                clearInterval(checkSummary);
                setTimeout(() => window.print(), 200); // Small delay to ensure content is rendered
            } else if (attempts >= maxAttempts) {
                clearInterval(checkSummary);
                // Print anyway if summary takes too long
                window.print();
            }
        }, 500);
    }
    
    // Close dropdown
    document.getElementById('exportDropdown').classList.add('hidden');
}
</script>

@endsection 