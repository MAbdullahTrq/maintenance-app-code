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
                    <label for="property_ids" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-2 text-purple-500"></i>Properties (Optional)
                    </label>
                    <select name="property_ids[]" id="property_ids" multiple 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            size="4">
                        <option value="">All Properties</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }} - {{ $property->address }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple properties</p>
                </div>

                <!-- Technician Filter -->
                <div class="md:col-span-2">
                    <label for="technician_ids" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tools mr-2 text-orange-500"></i>Technicians (Optional)
                    </label>
                    <select name="technician_ids[]" id="technician_ids" multiple 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            size="3">
                        <option value="">All Technicians</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }} - {{ $technician->email }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple technicians</p>
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
    const propertySelect = document.getElementById('property_ids');
    const technicianSelect = document.getElementById('technician_ids');
    const reportPreview = document.getElementById('reportPreview');
    const reportDescription = document.getElementById('reportDescription');

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
                        propertySelect.innerHTML = '<option value="">All Properties</option>';
                        properties.forEach(property => {
                            const option = document.createElement('option');
                            option.value = property.id;
                            option.textContent = `${property.name} - ${property.address}`;
                            propertySelect.appendChild(option);
                        });
                    });
            } else {
                // Reset to all properties
                location.reload();
            }
            updateReportPreview();
        });
    }

    // Handle property change - filter technicians
    propertySelect.addEventListener('change', function() {
        const selectedProperties = Array.from(this.selectedOptions).map(option => option.value).filter(val => val);
        
        if (selectedProperties.length > 0) {
            fetch(`{{ route('api.technicians-by-properties') }}?property_ids[]=${selectedProperties.join('&property_ids[]=')}`)
                .then(response => response.json())
                .then(technicians => {
                    technicianSelect.innerHTML = '<option value="">All Technicians</option>';
                    technicians.forEach(technician => {
                        const option = document.createElement('option');
                        option.value = technician.id;
                        option.textContent = `${technician.name} - ${technician.email}`;
                        technicianSelect.appendChild(option);
                    });
                });
        }
        updateReportPreview();
    });

    // Update preview when technician changes
    technicianSelect.addEventListener('change', updateReportPreview);

    function updateReportPreview() {
        const owner = ownerSelect ? ownerSelect.value : '';
        const properties = Array.from(propertySelect.selectedOptions).map(option => option.value).filter(val => val);
        const technicians = Array.from(technicianSelect.selectedOptions).map(option => option.value).filter(val => val);
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