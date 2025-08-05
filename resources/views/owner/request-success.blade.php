@extends('layouts.guest')

@section('title', 'Request Submitted Successfully')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Request Submitted Successfully!</h2>
            <p class="text-gray-600 mb-6">Your maintenance request has been submitted and will be reviewed by your property manager.</p>
            
            <!-- Owner Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">Owner Information</h3>
                <p class="text-blue-700"><strong>Name:</strong> {{ $owner->display_name }}</p>
                @if($owner->email)
                    <p class="text-blue-700"><strong>Email:</strong> {{ $owner->email }}</p>
                @endif
            </div>
            
            <!-- Next Steps -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">What happens next?</h3>
                <div class="space-y-3 text-left">
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <p class="text-gray-700 font-medium">Review Process</p>
                            <p class="text-gray-600 text-sm">Your property manager will review your request and determine the appropriate action.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">2</span>
                        </div>
                        <div>
                            <p class="text-gray-700 font-medium">Assignment</p>
                            <p class="text-gray-600 text-sm">If approved, a technician will be assigned to handle the maintenance work.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">3</span>
                        </div>
                        <div>
                            <p class="text-gray-700 font-medium">Updates</p>
                            <p class="text-gray-600 text-sm">You'll receive updates on the progress of your maintenance request.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-yellow-800 mb-2">Need to make changes?</h3>
                <p class="text-yellow-700 text-sm mb-3">If you need to modify your request or have any questions, please contact your property manager:</p>
                <div class="text-sm">
                    <p class="text-gray-800"><strong>Manager:</strong> {{ $owner->manager->name }}</p>
                    <p class="text-gray-800"><strong>Email:</strong> {{ $owner->manager->email }}</p>
                    @if($owner->manager->phone)
                        <p class="text-gray-800"><strong>Phone:</strong> {{ $owner->manager->phone }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('owner.request.form', $owner->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Submit Another Request
                </a>
                
                <a href="{{ route('owner.info', $owner->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-home mr-2"></i>
                    Back to Owner Portal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 