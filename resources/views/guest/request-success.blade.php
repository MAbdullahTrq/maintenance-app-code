@extends('layouts.guest')

@section('title', 'Request Submitted')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8 text-center">
            <div class="rounded-full bg-green-100 p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-5xl"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Maintenance Request Submitted</h2>
            <p class="text-gray-600 mb-6">Thank you for submitting your maintenance request for {{ $property->name }}.</p>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h3 class="text-lg font-medium text-gray-800 mb-4">What happens next?</h3>
                
                <ul class="space-y-4">
                    <li class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-500">
                                1
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-700">Your request will be reviewed by the property manager.</p>
                        </div>
                    </li>
                    
                    <li class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-500">
                                2
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-700">Once approved, a technician will be assigned to your request.</p>
                        </div>
                    </li>
                    
                    <li class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-500">
                                3
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-700">If you provided contact information, you will receive updates on the status of your request.</p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('guest.request.form', $property->access_link) }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                    Submit Another Request
                </a>
                <a href="/" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200">
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 