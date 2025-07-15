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

    <!-- Filter Combinations Help -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Report Types:</strong> Select different combinations to generate specific reports - 
                    Owner only (all their properties), Property specific, Technician performance, or combine filters for detailed analysis.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <form id="reportForm" action="{{ route('reports.generate') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date Range -->
                <div class="md:col-span-2">
                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-blue-500"></i>Date Range <span class="text-red-500">*</span>
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
                <div id="customDateRange" class="md:col-span-2" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                <!-- Owner Filter -->
                @if($owners->count() > 1)
                <div>
                    <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-green-500"></i>Owner (Optional)
                    </label>
                    <select name="owner_id" id="owner_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Owners</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                    <input type="hidden" name="owner_id" value="{{ $owners->first()->id ?? '' }}">
                @endif

                <!-- Property Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-2 text-purple-500"></i>Properties (Optional)
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
                                    <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm">{{ $property->name }} - {{ $property->address }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Select one or more properties for the report</p>
                </div>

                <!-- Technician Filter -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tools mr-2 text-orange-500"></i>Technicians (Optional)
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
                                    <span class="text-sm">{{ $technician->name }} - {{ $technician->email }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Select one or more technicians for the report</p>
                </div>
            </div>

            <!-- Report Preview -->
            <div id="reportPreview" class="mt-6 p-4 bg-gray-50 rounded-lg" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Report Preview</h3>
                <p id="reportDescription" class="text-gray-600"></p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <button type="submit" name="format" value="web" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-chart-bar mr-2"></i>Generate Web Report
                </button>
                <button type="submit" name="format" value="csv" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-download mr-2"></i>Download CSV
                </button>
                <button type="submit" name="format" value="pdf" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <i class="fas fa-file-pdf mr-2"></i>Download PDF
                </button>
            </div>
        </form>
    </div>

    <!-- Combinations Supported Table -->
    <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-search mr-2 text-blue-500"></i>Combinations Supported
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result Type</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">All work across owner's properties</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Specific property under an owner</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Property-based report</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Tasks by a tech at a specific property</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">All work by a technician</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">✗</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">✓</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Full system report</td>
                        </tr>
                    </tbody>
                </table>
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

    // Select All functionality for properties
    const selectAllProperties = document.getElementById('select-all-properties');
    const propertyCheckboxes = document.querySelectorAll('.property-checkbox input[type="checkbox"]');
    
    if (selectAllProperties) {
        selectAllProperties.addEventListener('change', function() {
            propertyCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateReportPreview();
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
                        const propertyContainer = document.querySelector('.property-checkbox').parentNode;
                        propertyContainer.innerHTML = '';
                        
                        // Add select all option
                        propertyContainer.innerHTML = `
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all-properties" class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Select All Properties</span>
                            </label>
                            <hr class="border-gray-300">
                        `;
                        
                        // Add filtered properties
                        properties.forEach(property => {
                            const label = document.createElement('label');
                            label.className = 'flex items-center property-checkbox';
                            label.innerHTML = `
                                <input type="checkbox" name="property_ids[]" value="${property.id}" class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">${property.name} - ${property.address}</span>
                            `;
                            propertyContainer.appendChild(label);
                        });
                        
                        // Reattach event listeners
                        attachPropertyListeners();
                    });
            } else {
                // Reset to all properties
                location.reload();
            }
            updateReportPreview();
        });
    }

    // Handle property checkbox changes
    function attachPropertyListeners() {
        const newPropertyCheckboxes = document.querySelectorAll('.property-checkbox input[type="checkbox"]');
        const newSelectAllProperties = document.getElementById('select-all-properties');
        
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
                            const technicianContainer = document.querySelector('.technician-checkbox').parentNode;
                            technicianContainer.innerHTML = '';
                            
                            // Add select all option
                            technicianContainer.innerHTML = `
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all-technicians" class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Select All Technicians</span>
                                </label>
                                <hr class="border-gray-300">
                            `;
                            
                            // Add filtered technicians
                            technicians.forEach(technician => {
                                const label = document.createElement('label');
                                label.className = 'flex items-center technician-checkbox';
                                label.innerHTML = `
                                    <input type="checkbox" name="technician_ids[]" value="${technician.id}" class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm">${technician.name} - ${technician.email}</span>
                                `;
                                technicianContainer.appendChild(label);
                            });
                            
                            // Reattach technician listeners
                            attachTechnicianListeners();
                        });
                }
                updateReportPreview();
            });
        });
    }

    // Handle technician checkbox changes
    function attachTechnicianListeners() {
        const newTechnicianCheckboxes = document.querySelectorAll('.technician-checkbox input[type="checkbox"]');
        const newSelectAllTechnicians = document.getElementById('select-all-technicians');
        
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
    propertyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateReportPreview);
    });
    
    technicianCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateReportPreview);
    });

    function updateReportPreview() {
        const owner = ownerSelect ? ownerSelect.value : '';
        const properties = Array.from(document.querySelectorAll('input[name="property_ids[]"]:checked')).map(cb => cb.value);
        const technicians = Array.from(document.querySelectorAll('input[name="technician_ids[]"]:checked')).map(cb => cb.value);
        const dateRangeValue = dateRange.value;

        if (!dateRangeValue) {
            reportPreview.style.display = 'none';
            return;
        }

        let description = 'This report will show: ';
        
        if (owner && properties.length === 0 && technicians.length === 0) {
            description += 'All work across the selected owner\'s properties';
        } else if (owner && properties.length > 0 && technicians.length === 0) {
            description += `Work at ${properties.length} specific ${properties.length === 1 ? 'property' : 'properties'} under the selected owner`;
        } else if (!owner && properties.length > 0 && technicians.length === 0) {
            description += `All work at ${properties.length} selected ${properties.length === 1 ? 'property' : 'properties'}`;
        } else if (!owner && properties.length > 0 && technicians.length > 0) {
            description += `Work by ${technicians.length} selected ${technicians.length === 1 ? 'technician' : 'technicians'} at ${properties.length} ${properties.length === 1 ? 'property' : 'properties'}`;
        } else if (!owner && properties.length === 0 && technicians.length > 0) {
            description += `All work by ${technicians.length} selected ${technicians.length === 1 ? 'technician' : 'technicians'}`;
        } else {
            description += 'Full system report for all properties and technicians';
        }

        description += ` for the selected date range.`;

        reportDescription.textContent = description;
        reportPreview.style.display = 'block';
    }

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

            // Update the date_range value to include custom dates
            dateRange.value = `${startDate} to ${endDate}`;
        }
    });
});
</script>
@endsection 