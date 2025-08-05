@extends('layouts.guest')

@section('title', 'Owner Information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Maintenance Request Portal</h2>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Owner Information</h3>
                    <p class="text-blue-700"><strong>Name:</strong> {{ $owner->display_name }}</p>
                    @if($owner->company)
                        <p class="text-blue-700"><strong>Company:</strong> {{ $owner->company }}</p>
                    @endif
                    @if($owner->email)
                        <p class="text-blue-700"><strong>Email:</strong> {{ $owner->email }}</p>
                    @endif
                    @if($owner->phone)
                        <p class="text-blue-700"><strong>Phone:</strong> {{ $owner->phone }}</p>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Properties Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Properties</h3>
                    @if($owner->properties->count() > 0)
                        <div class="space-y-3">
                            @foreach($owner->properties as $property)
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <h4 class="font-semibold text-gray-800">{{ $property->name }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $property->address }}</p>
                                    @if($property->description)
                                        <p class="text-gray-500 text-sm mt-1">{{ $property->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No properties found for this owner.</p>
                    @endif
                </div>
                
                <!-- Quick Actions Section -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Quick Actions</h3>
                    
                    @if($owner->properties->count() > 0)
                        <div class="space-y-4">
                            <div class="text-center">
                                <a href="{{ route('owner.request.form', $owner->id) }}" 
                                   class="inline-flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Submit Maintenance Request
                                </a>
                            </div>
                            
                            <div class="text-sm text-blue-700 text-center">
                                <p>Click the button above to submit a new maintenance request for any of your properties.</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-blue-700 mb-4">No properties available for maintenance requests.</p>
                            <p class="text-sm text-blue-600">Please contact your property manager to add properties to your account.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Instructions Section -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">How to Submit a Request</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                            <span class="text-blue-600 font-bold text-lg">1</span>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Select Property</h4>
                        <p class="text-gray-600 text-sm">Choose the property where the maintenance is needed</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                            <span class="text-blue-600 font-bold text-lg">2</span>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Describe Issue</h4>
                        <p class="text-gray-600 text-sm">Provide details about the maintenance issue and upload photos</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                            <span class="text-blue-600 font-bold text-lg">3</span>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Submit & Track</h4>
                        <p class="text-gray-600 text-sm">Submit your request and track its progress</p>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">Need Help?</h3>
                <p class="text-yellow-700 mb-3">If you have any questions or need assistance with your maintenance request, please contact your property manager:</p>
                <div class="bg-white rounded-lg p-4 border border-yellow-200">
                    <p class="text-gray-800"><strong>Manager:</strong> {{ $owner->manager->name }}</p>
                    <p class="text-gray-800"><strong>Email:</strong> {{ $owner->manager->email }}</p>
                    @if($owner->manager->phone)
                        <p class="text-gray-800"><strong>Phone:</strong> {{ $owner->manager->phone }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 