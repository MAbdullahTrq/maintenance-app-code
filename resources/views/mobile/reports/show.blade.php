@extends('mobile.layout')

@section('title', 'Report Results')

@section('header-actions')
<a href="{{ route('mobile.reports.create') }}" class="text-sm font-medium">New Report</a>
@endsection

@section('content')
<div class="flex justify-center px-4">
    <div class="bg-white rounded-xl shadow p-4 w-full max-w-6xl mx-auto print-content">
        <!-- Report Header with Export Controls -->
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">
                📊 Report{{ $owner_name ? ' for ' . $owner_name : '' }}
            </h1>
            
            @if(count($property_names) > 0 || count($technician_names) > 0)
                <div class="text-sm text-gray-600 mb-2 space-y-1">
                    @if(count($property_names) > 0)
                        <div>
                            <span class="font-medium">Properties:</span> 
                            {{ implode(', ', $property_names) }}
                        </div>
                    @endif
                    @if(count($technician_names) > 0)
                        <div>
                            <span class="font-medium">Technicians:</span> 
                            {{ implode(', ', $technician_names) }}
                        </div>
                    @endif
                </div>
            @endif
            
            <p class="text-xs text-gray-500">{{ $dateRange['label'] }}</p>
        </div>

        <!-- Export Actions -->
        <div class="mb-6 space-y-3 no-print">
            <!-- Export Dropdown -->
            <div class="relative">
                <button id="exportDropdownBtn" type="button" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center">
                    📤 Export
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="exportDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                    <button onclick="exportReport('csv')" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700 border-b border-gray-100">
                        📊 CSV Spreadsheet
                    </button>
                    <button onclick="exportReport('pdf')" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700 border-b border-gray-100">
                        📄 PDF Document
                    </button>
                    <button onclick="printReport()" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700">
                        🖨️ Print Report
                    </button>
                </div>
            </div>
            
            <!-- Generate AI Summary Button -->
            <button id="generateAISummary" class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">
                🧠 Generate AI summary
            </button>
        </div>

        <!-- AI Summary Section -->
        <div id="aiSummarySection" class="mb-6 no-print" style="display: none;">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <span class="text-blue-600 text-lg mr-2">🧠</span>
                    <h2 class="text-lg font-bold text-blue-800">AI Summary</h2>
                </div>
                <div id="aiSummaryContent" class="text-gray-700 text-sm leading-relaxed whitespace-pre-line"></div>
                <div id="aiSummaryLoading" class="flex items-center justify-center py-6">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-blue-600 text-sm">Generating AI summary...</span>
                </div>
                <div id="aiSummaryError" class="text-red-600 text-sm" style="display: none;"></div>
            </div>
        </div>

        <!-- Main Report Content -->
        @if($requests->isNotEmpty())
            <div class="space-y-3">
                @foreach($requests as $request)
                    <div class="bg-gray-50 rounded-lg p-4 cursor-pointer hover:bg-gray-100 border hover:border-blue-300 transition-colors request-item" onclick="window.open('{{ route('mobile.request.show', $request->id) }}', '_blank')">
                        <!-- Header Row -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $request->property->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $request->property->address ?? 'No address' }}</div>
                            </div>
                            <div class="flex space-x-2 ml-3">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    @if($request->priority == 'low') bg-green-100 text-green-800
                                    @elseif($request->priority == 'medium') bg-yellow-100 text-yellow-800
                                    @elseif($request->priority == 'high') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($request->priority) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Details Row -->
                        <div class="flex justify-between items-center">
                            <div class="text-xs text-gray-600">
                                📅 {{ $request->created_at->format('d M, Y') }} • {{ $request->created_at->format('H:i') }}
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    @if($request->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status == 'assigned') bg-blue-100 text-blue-800
                                    @elseif($request->status == 'started') bg-purple-100 text-purple-800
                                    @elseif($request->status == 'completed') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-eye text-blue-600 text-xs"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Row -->
                        <div class="mt-2 text-sm font-medium text-blue-600">{{ $request->title }}</div>
                        
                        <!-- Tap instruction -->
                        <div class="mt-2 text-blue-500 text-xs action-button">
                            👆 Tap to view details
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <div class="text-4xl mb-3">📭</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No requests found</h3>
                <p class="text-sm">No maintenance requests match the selected criteria.</p>
                <div class="mt-4">
                    <a href="{{ route('mobile.reports.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        📊 Create New Report
                    </a>
                </div>
            </div>
        @endif

        @if($requests->isNotEmpty())
        <!-- Back to Create -->
        <div class="text-center mt-6 no-print">
            <a href="{{ route('mobile.reports.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                📊 Create New Report
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    @media print {
        /* Hide all layout elements except report content */
        body > header,
        body > nav,
        .no-print, 
        .action-button {
            display: none !important;
        }
        
        /* Reset body and main container */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
        }
        
        main {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Style the print content container */
        .print-content {
            margin: 0 !important;
            padding: 20px !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            max-width: none !important;
            width: 100% !important;
        }
        
        .bg-white, .bg-gray-50 {
            background: white !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        
        .rounded-xl, .rounded-lg {
            border-radius: 0 !important;
        }
        
        /* Remove cursor and hover effects for print */
        .cursor-pointer {
            cursor: default !important;
        }
        
        .hover\:bg-gray-100:hover,
        .hover\:border-blue-300:hover {
            background-color: inherit !important;
            border-color: inherit !important;
        }
        
        /* Show AI summary in print */
        #aiSummarySection {
            display: block !important;
        }
        
        /* Ensure request items are not clickable in print */
        .request-item {
            pointer-events: none !important;
        }
        
        /* Print-specific typography */
        h1 {
            font-size: 24px !important;
            margin-bottom: 10px !important;
        }
        
        /* Ensure content takes full width */
        .flex.justify-center {
            justify-content: flex-start !important;
        }
        
        .px-4 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    }
    
    /* Dropdown styles */
    .dropdown-open {
        display: block !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    const generateBtn = document.getElementById('generateAISummary');
    const summarySection = document.getElementById('aiSummarySection');
    const summaryContent = document.getElementById('aiSummaryContent');
    const summaryLoading = document.getElementById('aiSummaryLoading');
    const summaryError = document.getElementById('aiSummaryError');

    generateBtn.addEventListener('click', function() {
        summarySection.style.display = 'block';
        summaryContent.style.display = 'none';
        summaryLoading.style.display = 'flex';
        summaryError.style.display = 'none';
        
        generateBtn.disabled = true;
        generateBtn.innerHTML = '⏳ Generating...';

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

        fetch('{{ route("mobile.reports.ai-summary") }}', {
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
            generateBtn.innerHTML = '🧠 Generate AI summary';
        });
    });
});

// Export report function
function exportReport(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = format === 'csv' ? '{{ route("mobile.reports.csv") }}' : '{{ route("mobile.reports.pdf") }}';
    if (format === 'pdf') {
        form.target = '_blank';
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