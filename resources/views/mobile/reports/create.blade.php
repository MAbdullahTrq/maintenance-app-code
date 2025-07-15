@extends('mobile.layout')

@section('title', 'Create Report')

@section('header-actions')
<a href="{{ route('mobile.manager.all-requests') }}" class="text-sm font-medium">Back to Requests</a>
@endsection

@section('content')
<div class="flex justify-center px-4">
    <div class="bg-white rounded-xl shadow p-4 w-full max-w-4xl mx-auto">
        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-900 mb-2">Create Report</h1>
            <p class="text-sm text-gray-600">Generate maintenance reports with custom filters</p>
        </div>

        <form id="mobileReportForm" action="{{ route('mobile.reports.generate') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Date Range -->
            <div>
                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Date Range *
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

            <!-- Owner Filter -->
            @if($owners->count() > 1)
            <div>
                <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">
                    👤 Owner (Optional)
                </label>
                <select name="owner_id" id="owner_id" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Owners</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="owner_id" value="{{ $owners->first()->id ?? '' }}">
            @endif

            <!-- Properties Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    🏢 Properties (Optional)
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
                                <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4 mt-0.5">
                                <span class="text-sm">{{ $property->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Tap to select one or more properties</p>
            </div>

            <!-- Technicians Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    🔧 Technicians (Optional)
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

            <!-- Report Preview -->
            <div id="reportPreview" class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400" style="display: none;">
                <h3 class="text-sm font-semibold text-blue-800 mb-1">📊 Report Preview</h3>
                <p id="reportDescription" class="text-xs text-blue-700"></p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3 pt-4">
                <button type="submit" name="format" value="web" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-4 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    📊 Generate Web Report
                </button>
                
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="submitForm('csv')"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        📥 Download CSV
                    </button>
                    <button type="button" onclick="submitForm('pdf')"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        📄 Download PDF
                    </button>
                </div>
            </div>
        </form>

        <!-- Report Types Info -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">📋 Available Report Types</h3>
            <div class="space-y-2 text-xs text-gray-600">
                <div class="flex items-start space-x-2">
                    <span class="text-green-500 mt-0.5">✓</span>
                    <span><strong>Property Reports:</strong> Select specific properties to analyze</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-green-500 mt-0.5">✓</span>
                    <span><strong>Technician Reports:</strong> Track individual or multiple technician performance</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-green-500 mt-0.5">✓</span>
                    <span><strong>Combined Reports:</strong> Mix properties and technicians for detailed analysis</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-green-500 mt-0.5">✓</span>
                    <span><strong>Full System:</strong> Leave all filters empty for complete overview</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRange = document.getElementById('date_range');
    const customDateRange = document.getElementById('customDateRange');
    const ownerSelect = document.getElementById('owner_id');
    const reportPreview = document.getElementById('reportPreview');
    const reportDescription = document.getElementById('reportDescription');

    // Select All functionality for properties (mobile)
    const selectAllPropertiesMobile = document.getElementById('select-all-properties-mobile');
    const propertyCheckboxesMobile = document.querySelectorAll('.property-checkbox-mobile input[type="checkbox"]');
    
    if (selectAllPropertiesMobile) {
        selectAllPropertiesMobile.addEventListener('change', function() {
            propertyCheckboxesMobile.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateReportPreview();
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
            updateReportPreview();
        });
    }

    // Handle custom date range visibility
    dateRange.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
        }
        updateReportPreview();
    });

    // Handle owner change - filter properties
    if (ownerSelect) {
        ownerSelect.addEventListener('change', function() {
            const ownerId = this.value;
            
            if (ownerId) {
                fetch(`{{ route('api.properties-by-owner') }}?owner_id=${ownerId}`)
                    .then(response => response.json())
                    .then(properties => {
                        // Clear existing property checkboxes
                        const propertyContainer = document.querySelector('.property-checkbox-mobile').parentNode;
                        propertyContainer.innerHTML = '';
                        
                        // Add select all option
                        propertyContainer.innerHTML = `
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all-properties-mobile" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                <span class="text-sm font-medium text-gray-700">📋 Select All Properties</span>
                            </label>
                            <hr class="border-gray-300">
                        `;
                        
                        // Add filtered properties
                        properties.forEach(property => {
                            const label = document.createElement('label');
                            label.className = 'flex items-start property-checkbox-mobile';
                            label.innerHTML = `
                                <input type="checkbox" name="property_ids[]" value="${property.id}" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4 mt-0.5">
                                <span class="text-sm">${property.name}</span>
                            `;
                            propertyContainer.appendChild(label);
                        });
                        
                        // Clear technicians since properties changed
                        const technicianContainer = document.querySelector('.technician-checkbox-mobile').parentNode;
                        technicianContainer.innerHTML = `
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all-technicians-mobile" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                <span class="text-sm font-medium text-gray-700">👥 Select All Technicians</span>
                            </label>
                            <hr class="border-gray-300">
                        `;
                        
                        // Reattach event listeners
                        attachMobileListeners();
                    })
                    .catch(error => console.log('Error fetching properties:', error));
            } else {
                // Reset to all properties if no owner selected
                location.reload();
            }
            updateReportPreview();
        });
    }

    // Handle property checkbox changes (mobile)
    function attachMobileListeners() {
        const newPropertyCheckboxes = document.querySelectorAll('.property-checkbox-mobile input[type="checkbox"]');
        const newSelectAllProperties = document.getElementById('select-all-properties-mobile');
        
        if (newSelectAllProperties) {
            newSelectAllProperties.addEventListener('change', function() {
                newPropertyCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateReportPreview();
            });
        }
        
        newPropertyCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selectedProperties = Array.from(newPropertyCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                
                if (selectedProperties.length > 0) {
                    fetch(`{{ route('api.technicians-by-properties') }}?property_ids[]=${selectedProperties.join('&property_ids[]=')}`)
                        .then(response => response.json())
                        .then(technicians => {
                            // Clear existing technician checkboxes
                            const technicianContainer = document.querySelector('.technician-checkbox-mobile').parentNode;
                            technicianContainer.innerHTML = '';
                            
                            // Add select all option
                            technicianContainer.innerHTML = `
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all-technicians-mobile" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                    <span class="text-sm font-medium text-gray-700">👥 Select All Technicians</span>
                                </label>
                                <hr class="border-gray-300">
                            `;
                            
                            // Add filtered technicians
                            technicians.forEach(technician => {
                                const label = document.createElement('label');
                                label.className = 'flex items-center technician-checkbox-mobile';
                                label.innerHTML = `
                                    <input type="checkbox" name="technician_ids[]" value="${technician.id}" class="mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                    <span class="text-sm">${technician.name}</span>
                                `;
                                technicianContainer.appendChild(label);
                            });
                            
                            // Reattach technician listeners
                            attachMobileTechnicianListeners();
                        });
                }
                updateReportPreview();
            });
        });
    }

    // Handle technician checkbox changes (mobile)
    function attachMobileTechnicianListeners() {
        const newTechnicianCheckboxes = document.querySelectorAll('.technician-checkbox-mobile input[type="checkbox"]');
        const newSelectAllTechnicians = document.getElementById('select-all-technicians-mobile');
        
        if (newSelectAllTechnicians) {
            newSelectAllTechnicians.addEventListener('change', function() {
                newTechnicianCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateReportPreview();
            });
        }
        
        newTechnicianCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateReportPreview);
        });
    }

    // Initial attachment of listeners
    propertyCheckboxesMobile.forEach(checkbox => {
        checkbox.addEventListener('change', updateReportPreview);
    });
    
    technicianCheckboxesMobile.forEach(checkbox => {
        checkbox.addEventListener('change', updateReportPreview);
    });

    // Update preview when owner changes (if owner select exists)
    if (ownerSelect) {
        ownerSelect.addEventListener('change', updateReportPreview);
    }

    function updateReportPreview() {
        const owner = ownerSelect ? ownerSelect.value : '';
        const ownerText = ownerSelect && owner ? ownerSelect.options[ownerSelect.selectedIndex].text : '';
        const properties = Array.from(document.querySelectorAll('input[name="property_ids[]"]:checked')).map(cb => cb.value);
        const technicians = Array.from(document.querySelectorAll('input[name="technician_ids[]"]:checked')).map(cb => cb.value);
        const dateRangeValue = dateRange.value;

        if (!dateRangeValue) {
            reportPreview.style.display = 'none';
            return;
        }

        let description = '';
        
        // Add owner context if specified
        if (owner && ownerText) {
            description = `Report for ${ownerText}'s `;
        } else {
            description = 'Report for ';
        }
        
        if (properties.length === 0 && technicians.length === 0) {
            description += owner ? 'all properties and technicians' : 'all properties and technicians across all owners';
        } else if (properties.length > 0 && technicians.length === 0) {
            description += `${properties.length} selected ${properties.length === 1 ? 'property' : 'properties'}`;
        } else if (properties.length === 0 && technicians.length > 0) {
            description += `${technicians.length} selected ${technicians.length === 1 ? 'technician' : 'technicians'}`;
        } else {
            description += `${properties.length} ${properties.length === 1 ? 'property' : 'properties'} and ${technicians.length} ${technicians.length === 1 ? 'technician' : 'technicians'}`;
        }

        description += ` for the selected date range.`;

        reportDescription.textContent = description;
        reportPreview.style.display = 'block';
    }

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

            // Update the date_range value to include custom dates
            dateRange.value = `${startDate} to ${endDate}`;
        }

        // Show loading state
        const submitButtons = this.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            button.disabled = true;
            button.innerHTML = button.innerHTML.replace(/📊|📥|📄/, '⏳');
        });
    });
});

function submitForm(format) {
    const form = document.getElementById('mobileReportForm');
    const newForm = form.cloneNode(true);
    
    // Set the correct action based on format
    if (format === 'csv') {
        newForm.action = '/m/reports/csv';
    } else if (format === 'pdf') {
        newForm.action = '/m/reports/pdf';
        newForm.target = '_blank'; // Open PDF in new window
    }
    
    newForm.method = 'POST';
    newForm.style.display = 'none';
    document.body.appendChild(newForm);
    newForm.submit();
    document.body.removeChild(newForm); // Clean up
}
</script>
@endsection 