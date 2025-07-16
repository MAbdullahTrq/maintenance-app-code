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
                    <button type="submit" name="format" value="csv" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Export
                    </button>
                </form>
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

    <!-- Main Report Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($requests->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->property->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->property->address ?? 'No address' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($request->priority == 'low') bg-green-100 text-green-800
                                        @elseif($request->priority == 'medium') bg-yellow-100 text-yellow-800
                                        @elseif($request->priority == 'high') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('d M, Y') }}<br>
                                    <span class="text-xs">{{ $request->created_at->format('H:i') }}</span>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('maintenance.show', $request) }}" target="_blank" 
                                       class="w-8 h-8 bg-blue-100 hover:bg-blue-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-eye text-blue-600"></i>
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
</script>

@endsection 