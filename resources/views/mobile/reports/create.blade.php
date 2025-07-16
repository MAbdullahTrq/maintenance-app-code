@extends('mobile.layout')

@section('title', 'Create Report')

@section('header-actions')
<a href="{{ route('mobile.manager.all-requests') }}" class="text-sm font-medium">Back to Requests</a>
@endsection

@section('content')
<div class="flex justify-center px-4">
    <div class="bg-white rounded-xl shadow p-4 w-full max-w-md mx-auto">
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-900 mb-2">Create Report</h1>
            <p class="text-sm text-gray-600">Generate maintenance reports with custom filters</p>
        </div>

        <form id="mobileReportForm" action="{{ route('mobile.reports.generate') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Owner -->
            <div>
                <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Owner
                </label>
                <select name="owner_id" id="owner_id" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                           class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" id="end_date" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Generate Report Button -->
            <div class="pt-4">
                <button type="submit" name="format" value="web" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-4 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Generate Report
                </button>
            </div>
                
            <!-- Additional Export Options -->
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="submitForm('csv')"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    📥 CSV
                </button>
                <button type="button" onclick="submitForm('pdf')"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    📄 PDF
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
    document.getElementById('mobileReportForm').addEventListener('submit', function(e) {
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

        // Show loading state
        const submitButtons = this.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            button.disabled = true;
            button.innerHTML = '⏳ Generating...';
        });
    });
});

function submitForm(format) {
    const form = document.getElementById('mobileReportForm');
    
    // Validate form first
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Simple approach - modify form action and submit directly
    const originalAction = form.action;
    
    if (format === 'csv') {
        form.action = '/m/reports/csv';
    } else if (format === 'pdf') {
        form.action = '/m/reports/pdf';
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