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

            <!-- Properties Filter -->
            <div id="property-section-mobile" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Properties
                </label>
                <div class="border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto bg-gray-50">
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all-properties-mobile" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <span class="text-sm font-medium text-gray-700">📋 Select All Properties</span>
                        </label>
                        <hr class="border-gray-300">
                        @foreach($properties as $property)
                            <label class="flex items-start property-checkbox-mobile">
                                <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" data-owner="{{ $property->owner_id }}" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4 mt-0.5">
                                <span class="text-sm">{{ $property->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Tap to select one or more properties</p>
            </div>

            <!-- Technicians Filter -->
            <div id="technician-section-mobile" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Technicians
                </label>
                <div class="border border-gray-300 rounded-lg p-3 max-h-40 overflow-y-auto bg-gray-50">
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all-technicians-mobile" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <span class="text-sm font-medium text-gray-700">👥 Select All Technicians</span>
                        </label>
                        <hr class="border-gray-300">
                        @foreach($technicians as $technician)
                            <label class="flex items-center technician-checkbox-mobile">
                                <input type="checkbox" name="technician_ids[]" value="{{ $technician->id }}" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                <span class="text-sm">{{ $technician->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Tap to select one or more technicians</p>
            </div>

            <!-- Date Range -->
            <div id="date-range-section-mobile" style="display: none;">
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
            <div id="generate-button-section-mobile" class="pt-4" style="display: none;">
                <button type="submit" name="format" value="web" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-4 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Generate Report
                </button>
            </div>
                
            <!-- Additional Export Options -->
            <!-- <div id="export-buttons-section-mobile" class="grid grid-cols-3 gap-3" style="display: none;">
                <button type="button" onclick="submitForm('csv')"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    📥 CSV
                </button>
                <button type="button" onclick="submitForm('pdf')"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    📄 PDF
                </button>
                <button type="button" onclick="submitForm('docx')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    📝 DOCX
                </button>
            </div> -->
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRange = document.getElementById('date_range');
    const customDateRange = document.getElementById('customDateRange');
    const ownerSelect = document.getElementById('owner_id');
    const propertySectionMobile = document.getElementById('property-section-mobile');
    const technicianSectionMobile = document.getElementById('technician-section-mobile');
    const dateRangeSectionMobile = document.getElementById('date-range-section-mobile');
    const generateButtonSectionMobile = document.getElementById('generate-button-section-mobile');
    const exportButtonsSectionMobile = document.getElementById('export-buttons-section-mobile');

    // Select All functionality for properties (mobile)
    const selectAllPropertiesMobile = document.getElementById('select-all-properties-mobile');
    const propertyCheckboxesMobile = document.querySelectorAll('.property-checkbox-mobile input[type="checkbox"]');
    
    if (selectAllPropertiesMobile) {
        selectAllPropertiesMobile.addEventListener('change', function() {
            propertyCheckboxesMobile.forEach(checkbox => {
                if (checkbox.closest('.property-checkbox-mobile').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
        });
    }

    // Select All functionality for technicians (mobile)
    const selectAllTechniciansMobile = document.getElementById('select-all-technicians-mobile');
    const technicianCheckboxesMobile = document.querySelectorAll('.technician-checkbox-mobile input[type="checkbox"]');
    
    if (selectAllTechniciansMobile) {
        selectAllTechniciansMobile.addEventListener('change', function() {
            technicianCheckboxesMobile.forEach(checkbox => {
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
            generateButtonSectionMobile.style.display = 'block';
            exportButtonsSectionMobile.style.display = 'grid';
        } else {
            generateButtonSectionMobile.style.display = 'none';
            exportButtonsSectionMobile.style.display = 'none';
        }
    });

    // Handle owner change - show/hide sections and filter properties
    ownerSelect.addEventListener('change', function() {
        const ownerId = this.value;
        
        if (ownerId) {
            // Show property and technician sections
            propertySectionMobile.style.display = 'block';
            technicianSectionMobile.style.display = 'block';
            dateRangeSectionMobile.style.display = 'block';
            
            // Filter properties based on selected owner
            propertyCheckboxesMobile.forEach(checkbox => {
                const propertyContainer = checkbox.closest('.property-checkbox-mobile');
                
                if (checkbox.dataset.owner === ownerId) {
                    propertyContainer.style.display = 'flex';
                } else {
                    propertyContainer.style.display = 'none';
                    checkbox.checked = false; // Uncheck hidden properties
                }
            });
        } else {
            // Hide all sections if no owner selected
            propertySectionMobile.style.display = 'none';
            technicianSectionMobile.style.display = 'none';
            dateRangeSectionMobile.style.display = 'none';
            generateButtonSectionMobile.style.display = 'none';
            exportButtonsSectionMobile.style.display = 'none';
            
            // Reset form
            dateRange.value = '';
            customDateRange.style.display = 'none';
            
            // Uncheck all checkboxes
            propertyCheckboxesMobile.forEach(checkbox => checkbox.checked = false);
            technicianCheckboxesMobile.forEach(checkbox => checkbox.checked = false);
        }
        
        // Reset "Select All" states
        selectAllPropertiesMobile.checked = false;
        selectAllTechniciansMobile.checked = false;
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
    } else if (format === 'docx') {
        form.action = '/m/reports/docx';
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