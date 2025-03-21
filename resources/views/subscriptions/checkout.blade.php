@extends('layouts.app')

@section('title', 'Checkout')
@section('header', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Complete Your Subscription</h2>
                <p class="text-gray-600 mt-2">You're subscribing to the {{ $plan->name }} plan.</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Order Summary</h3>
                
                <div class="border-t border-gray-200 pt-4">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Plan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $plan->name }}</dd>
                        </div>
                        
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($plan->price, 2) }}</dd>
                        </div>
                        
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Duration</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $plan->duration }} month</dd>
                        </div>
                        
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Property Limit</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $plan->property_limit }}</dd>
                        </div>
                        
                        <div class="py-3 flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Technician Limit</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $plan->technician_limit }}</dd>
                        </div>
                        
                        <div class="py-3 flex justify-between">
                            <dt class="text-lg font-bold text-gray-800">Total</dt>
                            <dd class="text-lg font-bold text-gray-900">${{ number_format($plan->price, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Payment Method</h3>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <input id="paypal" name="payment_method" type="radio" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="paypal" class="ml-3 block text-sm font-medium text-gray-700">
                                PayPal
                            </label>
                        </div>
                        <div>
                            <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" alt="PayPal" class="h-6">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <!-- PayPal Button Container (JavaScript) -->
                        <div id="paypal-button-container" class="flex justify-center"></div>
                        
                        <!-- Manual PayPal Button (Fallback) -->
                        <div id="manual-paypal-button" class="mt-4 text-center">
                            <form action="{{ route('subscription.paypal.create', $plan->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png" alt="PayPal" class="h-5 mr-2">
                                    Checkout with PayPal
                                </button>
                            </form>
                        </div>
                        
                        <div id="paypal-error" class="text-red-500 text-sm mt-2 text-center hidden"></div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <a href="{{ route('subscription.plans') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Back to Plans
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide manual button if JavaScript is enabled
        document.getElementById('manual-paypal-button').style.display = 'none';
        
        // Render the PayPal button
        paypal.Buttons({
            // Set up the transaction
            createOrder: function(data, actions) {
                document.getElementById('paypal-error').classList.add('hidden');
                
                return fetch('{{ route('subscription.paypal.create', $plan->id) }}', {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(function(response) {
                    return response.json();
                }).then(function(data) {
                    if (data.error) {
                        document.getElementById('paypal-error').textContent = data.error;
                        document.getElementById('paypal-error').classList.remove('hidden');
                        return null;
                    }
                    return data.id;
                });
            },
            
            // Finalize the transaction
            onApprove: function(data, actions) {
                window.location.href = '{{ route('subscription.capture', $plan->id) }}?token=' + data.orderID;
            },
            
            // Handle errors
            onError: function(err) {
                document.getElementById('paypal-error').textContent = 'An error occurred. Please try again later.';
                document.getElementById('paypal-error').classList.remove('hidden');
                document.getElementById('manual-paypal-button').style.display = 'block';
                console.error(err);
            }
        }).render('#paypal-button-container');
    });
</script>
@endpush 