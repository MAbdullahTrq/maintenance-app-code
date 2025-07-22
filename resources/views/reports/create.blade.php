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
                <div id="property-section" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Property
                    </label>
                    <div class="border border-gray-300 rounded-md p-3 max-h-32 overflow-y-auto bg-gray-50">
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all-properties" class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Select All Properties</span>
                            </label>
                            <hr class="border-gray-300">
                            @foreach($properties as $property)
                                <label class="flex items-center property-checkbox">
                                    <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" data-owner="{{ $property->owner_id }}" class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm">{{ $property->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Select one or more properties</p>
                </div>

                <!-- Technician -->
                <div id="technician-section" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Technician
                    </label>
                    <div class="border border-gray-300 rounded-md p-3 max-h-32 overflow-y-auto bg-gray-50">
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all-technicians" class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Select All Technicians</span>
                            </label>
                            <hr class="border-gray-300">
                            @foreach($technicians as $technician)
                                <label class="flex items-center technician-checkbox">
                                    <input type="checkbox" name="technician_ids[]" value="{{ $technician->id }}" class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm">{{ $technician->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Select one or more technicians</p>
                </div>

                <!-- Date Range -->
                <div id="date-range-section" style="display: none;">
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
            <div id="generate-button-section" class="mt-6" style="display: none;">
                <button type="submit" name="format" value="web" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Generate Report
                </button>
            </div>

            <!-- Additional Export Options -->
            <div id="export-buttons-section" class="mt-4 grid grid-cols-3 gap-3" style="display: none;">
                <button type="button" onclick="submitForm('csv')"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    <i class="fas fa-download mr-1"></i>CSV
                </button>
                <button type="button" onclick="submitForm('pdf')" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    <i class="fas fa-file-pdf mr-1"></i>PDF
                </button>
                <button type="button" onclick="submitForm('docx')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <i class="fas fa-file-word mr-1"></i>DOCX
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
    const propertySection = document.getElementById('property-section');
    const technicianSection = document.getElementById('technician-section');
    const dateRangeSection = document.getElementById('date-range-section');
    const generateButtonSection = document.getElementById('generate-button-section');
    const exportButtonsSection = document.getElementById('export-buttons-section');

    // Select All functionality for properties
    const selectAllProperties = document.getElementById('select-all-properties');
    const propertyCheckboxes = document.querySelectorAll('.property-checkbox input[type="checkbox"]');
    
    if (selectAllProperties) {
        selectAllProperties.addEventListener('change', function() {
            propertyCheckboxes.forEach(checkbox => {
                if (checkbox.style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
        });
    }

    // Select All functionality for technicians  
    const selectAllTechnicians = document.getElementById('select-all-technicians');
    const technicianCheckboxes = document.querySelectorAll('.technician-checkbox input[type="checkbox"]');
    
    if (selectAllTechnicians) {
        selectAllTechnicians.addEventListener('change', function() {
            technicianCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Handle custom date range visibility
    dateRange.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
        }
        
        // Show generate buttons when date range is selected
        if (this.value) {
            generateButtonSection.style.display = 'block';
            exportButtonsSection.style.display = 'grid';
        } else {
            generateButtonSection.style.display = 'none';
            exportButtonsSection.style.display = 'none';
        }
    });

    // Handle owner change - show/hide sections and filter properties
    ownerSelect.addEventListener('change', function() {
        const ownerId = this.value;
        
        if (ownerId) {
            // Show property and technician sections
            propertySection.style.display = 'block';
            technicianSection.style.display = 'block';
            dateRangeSection.style.display = 'block';
            
            // Filter properties based on selected owner
            propertyCheckboxes.forEach(checkbox => {
                const propertyContainer = checkbox.closest('.property-checkbox');
                
                if (checkbox.dataset.owner === ownerId) {
                    propertyContainer.style.display = 'flex';
                } else {
                    propertyContainer.style.display = 'none';
                    checkbox.checked = false; // Uncheck hidden properties
                }
            });
        } else {
            // Hide all sections if no owner selected
            propertySection.style.display = 'none';
            technicianSection.style.display = 'none';
            dateRangeSection.style.display = 'none';
            generateButtonSection.style.display = 'none';
            exportButtonsSection.style.display = 'none';
            
            // Reset form
            dateRange.value = '';
            customDateRange.style.display = 'none';
            
            // Uncheck all checkboxes
            propertyCheckboxes.forEach(checkbox => checkbox.checked = false);
            technicianCheckboxes.forEach(checkbox => checkbox.checked = false);
        }
        
        // Reset "Select All" states
        selectAllProperties.checked = false;
        selectAllTechnicians.checked = false;
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
    } else if (format === 'docx') {
        form.action = '/reports/docx';
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