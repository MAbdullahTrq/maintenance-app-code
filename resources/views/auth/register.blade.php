@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<style>
    /* Turnstile responsive styles for desktop */
    .turnstile-container {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        overflow: visible;
        display: flex;
        justify-content: center;
    }
    
    .cf-turnstile {
        /* Let flexible size handle its own dimensions */
    }
    
    /* Only scale on very small screens (mobile fallback) */
    @media (max-width: 320px) {
        .cf-turnstile {
            transform: scale(0.9);
            transform-origin: center center;
        }
        .turnstile-container {
            max-width: 270px;
        }
    }

    /* Phone input styling */
    .phone-container {
        display: flex;
        gap: 0;
    }
    
    .country-select {
        width: 120px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: none;
    }
    
    .phone-input {
        flex: 1;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .phone-feedback {
        font-size: 12px;
        margin-top: 4px;
        transition: all 0.3s ease;
    }
    
    .phone-feedback.invalid {
        color: #dc2626;
    }
    
    .phone-feedback.valid {
        color: #16a34a;
    }
</style>

<div class="flex justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create an Account</h2>
                
                <form method="POST" action="{{ route('web.register') }}" id="registerForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 text-sm font-medium mb-2">Phone Number</label>
                        <div class="phone-container">
                            <select id="country_code" name="country_code" 
                                class="country-select appearance-none border py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country_code') border-red-500 @enderror">
                                @foreach($countries as $code => $country)
                                    <option value="{{ $code }}" {{ ($userCountry == $code || old('country_code') == $code) ? 'selected' : '' }}>
                                        {{ $country['code'] }} {{ $country['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                class="phone-input appearance-none border py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                                placeholder="Enter your phone number">
                        </div>
                        <div id="phone-feedback" class="phone-feedback"></div>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('country_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <input id="password" type="password" name="password" required
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="appearance-none border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Cloudflare Turnstile -->
                    <div class="mb-6">
                        <div class="flex justify-center">
                            <div class="turnstile-container">
                                <div class="cf-turnstile" 
                                     data-sitekey="{{ config('services.turnstile.site_key') }}"
                                     data-theme="light"
                                     data-size="flexible"></div>
                            </div>
                        </div>
                        @error('cf-turnstile-response')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <button type="submit" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Register
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="text-center text-sm text-gray-600">
                    Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const countrySelect = document.getElementById('country_code');
    const feedbackDiv = document.getElementById('phone-feedback');
    const submitBtn = document.getElementById('submitBtn');
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