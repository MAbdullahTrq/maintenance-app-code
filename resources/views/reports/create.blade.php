@extends('layouts.app')

@section('title', 'Create Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Report</h1>
        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Back to Requests
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-md mx-auto">
        <form id="reportForm" action="{{ route('reports.generate') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Owner -->
                <div>
                    <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Owner
                    </label>
                    <select name="owner_id" id="owner_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Owner</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Property -->
                <div>
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Property
                    </label>
                    <select name="property_id" id="property_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" data-owner="{{ $property->owner_id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Technician -->
                <div>
                    <label for="technician_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Technician
                    </label>
                    <select name="technician_id" id="technician_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Technician</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-2">
                        Date Range
                    </label>
                    <select name="date_range" id="date_range" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Date Range</option>
                        <option value="last_7_days">Last 7 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div id="customDateRange" class="space-y-3" style="display: none;">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Generate Report Button -->
            <div class="mt-6">
                <button type="submit" name="format" value="web" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Generate Report
                </button>
            </div>

            <!-- Additional Export Options -->
            <div class="mt-4 grid grid-cols-2 gap-3">
                <button type="button" onclick="submitForm('csv')"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    <i class="fas fa-download mr-1"></i>CSV
                </button>
                <button type="button" onclick="submitForm('pdf')" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    <i class="fas fa-file-pdf mr-1"></i>PDF
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRange = document.getElementById('date_range');
    const customDateRange = document.getElementById('customDateRange');
    const ownerSelect = document.getElementById('owner_id');
    const propertySelect = document.getElementById('property_id');

    // Handle custom date range visibility
    dateRange.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
        }
    });

    // Handle owner change - filter properties
    ownerSelect.addEventListener('change', function() {
        const ownerId = this.value;
        const propertyOptions = propertySelect.querySelectorAll('option');
        
        propertySelect.value = ''; // Reset property selection
        
        propertyOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block'; // Always show "Select Property"
            } else if (ownerId === '' || option.dataset.owner === ownerId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Form validation
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const dateRangeValue = dateRange.value;
        
        if (!dateRangeValue) {
            e.preventDefault();
            alert('Please select a date range.');
            return;
        }

        if (dateRangeValue === 'custom') {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (!startDate || !endDate) {
                e.preventDefault();
                alert('Please select both start and end dates for custom range.');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('Start date cannot be after end date.');
                return;
            }
        }
    });
});

function submitForm(format) {
    const form = document.getElementById('reportForm');
    
    // Validate form first
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Simple approach - modify form action and submit directly
    const originalAction = form.action;
    
    if (format === 'csv') {
        form.action = '/reports/csv';
    } else if (format === 'pdf') {
        form.action = '/reports/pdf';
        form.target = '_blank';
    }
    
    // Submit the form
    form.submit();
    
    // Reset form action
    setTimeout(() => {
        form.action = originalAction;
        form.target = '';
    }, 100);
}
</script>
@endsection 