@extends('mobile.layout')

@section('title', 'Register')

@push('styles')
<style>
    /* Turnstile responsive styles for mobile */
    .turnstile-container {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        overflow: visible;
        display: flex;
        justify-content: center;
    }
    
    .cf-turnstile {
        transform: scale(0.85);
        transform-origin: center center;
    }
    
    @media (max-width: 320px) {
        .cf-turnstile {
            transform: scale(0.75);
        }
        .turnstile-container {
            max-width: 240px;
        }
    }

    /* Phone input styling for mobile */
    .phone-container {
        display: flex;
        gap: 0;
        margin-bottom: 8px;
    }
    
    .country-select {
        width: 100px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: none;
        font-size: 12px;
        padding: 12px 8px;
    }
    
    .phone-input {
        flex: 1;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .phone-feedback {
        font-size: 11px;
        margin-top: 4px;
        transition: all 0.3s ease;
        min-height: 16px;
    }
    
    .phone-feedback.invalid {
        color: #dc2626;
    }
    
    .phone-feedback.valid {
        color: #16a34a;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-blue-50 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-center" style="padding-top: min(25vh, 120px)">
    <div class="w-full max-w-md space-y-8">
        <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
            <div class="mb-6 text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create Account</h1>
                <p class="mt-2 text-sm text-gray-600">Join MaintainXtra today</p>
            </div>
            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4" id="mobileRegisterForm">
                @csrf
                <div>
                    <label for="name" class="sr-only">Full Name</label>
                    <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Email address" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="mobile_phone" class="sr-only">Phone Number</label>
                    <div class="phone-container">
                        <select id="mobile_country_code" name="country_code" 
                            class="country-select border border-gray-300 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('country_code') border-red-500 @enderror">
                            @foreach($countries as $code => $country)
                                <option value="{{ $code }}" {{ ($userCountry == $code || old('country_code') == $code) ? 'selected' : '' }}>
                                    {{ $country['code'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="tel" name="phone" id="mobile_phone" 
                            class="phone-input w-full border border-gray-300 p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                            placeholder="Phone number" value="{{ old('phone') }}" required>
                    </div>
                    <div id="mobile-phone-feedback" class="phone-feedback"></div>
                    @error('phone')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                    @error('country_code')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Password" required>
                    @error('password')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Confirm Password" required>
                </div>
                
                <!-- Cloudflare Turnstile -->
                <div class="flex justify-center w-full">
                    <div class="turnstile-container">
                        <div class="cf-turnstile" 
                             data-sitekey="{{ config('services.turnstile.site_key') }}"
                             data-theme="light"
                             data-size="flexible"></div>
                    </div>
                </div>
                @error('cf-turnstile-response')
                    <div class="text-red-500 text-xs text-center">{{ $message }}</div>
                @enderror
                
                <button type="submit" id="mobileSubmitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold text-sm transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    Create Account
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Sign in</a>
                </p>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('mobile_phone');
    const countrySelect = document.getElementById('mobile_country_code');
    const feedbackDiv = document.getElementById('mobile-phone-feedback');
    const submitBtn = document.getElementById('mobileSubmitBtn');
    let validationTimeout;
    let isPhoneValid = false;

    function validatePhone() {
        const phone = phoneInput.value.trim();
        const country = countrySelect.value;
        
        if (!phone) {
            feedbackDiv.textContent = '';
            feedbackDiv.className = 'phone-feedback';
            isPhoneValid = false;
            updateSubmitButton();
            return;
        }

        // Show loading state
        feedbackDiv.textContent = 'Validating...';
        feedbackDiv.className = 'phone-feedback';

        // Clear previous timeout
        clearTimeout(validationTimeout);
        
        // Validate after 500ms delay
        validationTimeout = setTimeout(() => {
            fetch('/api/validate-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone: phone,
                    country: country
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    feedbackDiv.textContent = '✓ Valid phone number';
                    feedbackDiv.className = 'phone-feedback valid';
                    isPhoneValid = true;
                } else {
                    feedbackDiv.textContent = '✗ ' + (data.message || 'Invalid phone number format');
                    feedbackDiv.className = 'phone-feedback invalid';
                    isPhoneValid = false;
                }
                updateSubmitButton();
            })
            .catch(error => {
                feedbackDiv.textContent = '✗ Unable to validate phone number';
                feedbackDiv.className = 'phone-feedback invalid';
                isPhoneValid = false;
                updateSubmitButton();
            });
        }, 500);
    }

    function updateSubmitButton() {
        if (isPhoneValid && phoneInput.value.trim()) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Real-time validation
    phoneInput.addEventListener('input', validatePhone);
    countrySelect.addEventListener('change', validatePhone);

    // Initial validation if there's an old value
    if (phoneInput.value) {
        validatePhone();
    }
});
</script>
@endsection 