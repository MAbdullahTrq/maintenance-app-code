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

    /* Single Phone input styling for mobile */
    .phone-input-container {
        position: relative;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: white;
    }
    
    .phone-input-container:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .phone-input-wrapper {
        display: flex;
        align-items: center;
        gap: 0;
    }
    
    .phone-country-code {
        width: 60px;
        border: none;
        padding: 14px 8px 14px 16px;
        font-size: 14px;
        outline: none;
        background: transparent;
        font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
        font-weight: 600;
        color: #374151;
        border-right: 1px solid #e5e7eb;
        text-align: center;
    }
    
    .phone-country-code::placeholder {
        color: #9ca3af;
        font-family: inherit;
    }
    
    .phone-number-input {
        flex: 1;
        border: none;
        padding: 14px 16px;
        font-size: 14px;
        outline: none;
        background: transparent;
        font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
    }
    
    .phone-number-input::placeholder {
        color: #9ca3af;
        font-family: inherit;
    }
    
    .phone-input {
        flex: 1;
        border: none;
        padding: 14px 16px;
        font-size: 14px;
        outline: none;
        background: white;
    }
    
    .phone-input::placeholder {
        color: #9ca3af;
        font-size: 13px;
    }
    
    .phone-feedback {
        font-size: 11px;
        margin-top: 15px;
        transition: all 0.3s ease;
        min-height: 16px;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 0 4px;
    }
    
    .phone-feedback.invalid {
        color: #dc2626;
    }
    
    .phone-feedback.valid {
        color: #16a34a;
    }
    
    .phone-feedback-icon {
        font-size: 11px;
    }
    
    .phone-example {
        font-size: 10px;
        color: #6b7280;
        margin-top: 15px;
        padding: 0 4px;
        font-style: italic;
    }
    
    /* Custom scrollbar styling for mobile country dropdown */
    .country-options-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .country-options-list::-webkit-scrollbar-track {
        background: rgba(243, 244, 246, 0.5);
        border-radius: 3px;
    }
    
    .country-options-list::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 3px;
        transition: all 0.2s ease;
    }
    
    .country-options-list::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        transform: scaleX(1.2);
    }
    
    /* Smooth scrolling */
    .country-options-list {
        scroll-behavior: smooth;
    }
    
    /* Mobile form enhancements */
    .form-input {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 14px 16px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .form-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    
    .form-input.error {
        border-color: #dc2626;
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
                    <input type="text" name="name" id="name" 
                        class="form-input w-full placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') error @enderror" 
                        placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" name="email" id="email" 
                        class="form-input w-full placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') error @enderror" 
                        placeholder="Email address" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="mobile_phone" class="sr-only">Phone Number</label>
                    <div class="phone-input-container @error('phone') border-red-500 @enderror">
                        <div class="phone-input-wrapper">
                            <input type="text" id="mobile_country_code_input" 
                                class="phone-country-code"
                                placeholder="+1">
                            <input type="tel" name="phone" id="mobile_phone" 
                                class="phone-number-input"
                                placeholder="555 123 4567" value="{{ old('phone') }}" required>
                        </div>
                        <input type="hidden" name="country_code" id="mobile_country_code" value="{{ old('country_code', $userCountry) }}">
                    </div>
                    <div id="mobile-phone-feedback" class="phone-feedback" style="margin-top: 15px;"></div>
                    <div id="mobile-phone-example" class="phone-example" style="margin-top: 15px;"></div>
                    @error('phone')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" name="password" id="password" 
                        class="form-input w-full placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') error @enderror" 
                        placeholder="Password" required>
                    @error('password')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                        class="form-input w-full placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Confirm Password" required>
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
    // Country data with flags
    const countryData = @json($countries);
    const countryFlags = {
        'US': '🇺🇸', 'CA': '🇨🇦', 'GB': '🇬🇧', 'AU': '🇦🇺', 'DE': '🇩🇪', 'FR': '🇫🇷', 'IT': '🇮🇹', 'ES': '🇪🇸',
        'NL': '🇳🇱', 'BE': '🇧🇪', 'CH': '🇨🇭', 'AT': '🇦🇹', 'SE': '🇸🇪', 'NO': '🇳🇴', 'DK': '🇩🇰', 'FI': '🇫🇮',
        'IE': '🇮🇪', 'PT': '🇵🇹', 'GR': '🇬🇷', 'PL': '🇵🇱', 'CZ': '🇨🇿', 'HU': '🇭🇺', 'SK': '🇸🇰', 'SI': '🇸🇮',
        'HR': '🇭🇷', 'BG': '🇧🇬', 'RO': '🇷🇴', 'LT': '🇱🇹', 'LV': '🇱🇻', 'EE': '🇪🇪', 'RU': '🇷🇺', 'UA': '🇺🇦',
        'BY': '🇧🇾', 'TR': '🇹🇷', 'IL': '🇮🇱', 'SA': '🇸🇦', 'AE': '🇦🇪', 'IN': '🇮🇳', 'CN': '🇨🇳', 'JP': '🇯🇵',
        'KR': '🇰🇷', 'SG': '🇸🇬', 'MY': '🇲🇾', 'TH': '🇹🇭', 'PH': '🇵🇭', 'ID': '🇮🇩', 'VN': '🇻🇳', 'BR': '🇧🇷',
        'MX': '🇲🇽', 'AR': '🇦🇷', 'CL': '🇨🇱', 'CO': '🇨🇴', 'PE': '🇵🇪', 'ZA': '🇿🇦', 'EG': '🇪🇬', 'NG': '🇳🇬',
        'KE': '🇰🇪', 'GH': '🇬🇭', 'MA': '🇲🇦', 'TN': '🇹🇳', 'DZ': '🇩🇿', 'PK': '🇵🇰', 'BD': '🇧🇩', 'LK': '🇱🇰', 'NZ': '🇳🇿'
    };

    // DOM elements
    const phoneInput = document.getElementById('mobile_phone');
    const countryCodeInput = document.getElementById('mobile_country_code');
    const countryCodeDisplay = document.getElementById('mobile_country_code_input');
    const feedbackDiv = document.getElementById('mobile-phone-feedback');
    const exampleDiv = document.getElementById('mobile-phone-example');
    const submitBtn = document.getElementById('mobileSubmitBtn');

    let validationTimeout;
    let isPhoneValid = false;
    let detectedCountry = null;

    // Initialize phone input
    function initializePhoneInput() {
        // Priority order for country selection:
        // 1. Old form value (from validation errors)
        // 2. User's previous selection (from localStorage)
        // 3. Default country (US)
        
        let selectedCountry = 'US'; // Default fallback
        
        // Check for old form value first (highest priority)
        const oldCountryCode = '{{ old('country_code') }}';
        if (oldCountryCode) {
            selectedCountry = oldCountryCode;
        } else {
            // Check if user has a previously selected country in localStorage
            const savedCountry = localStorage.getItem('selectedCountryCode');
            if (savedCountry && countryData[savedCountry]) {
                selectedCountry = savedCountry;
            }
        }
        
        const country = countryData[selectedCountry];
        if (country) {
            countryCodeDisplay.value = country.code;
            countryCodeInput.value = selectedCountry;
            phoneInput.placeholder = `555 123 4567`;
            
            // Save the selection to localStorage for persistence
            localStorage.setItem('selectedCountryCode', selectedCountry);
        }
    }

    function detectCountryFromCode(code) {
        if (!code) {
            return null;
        }
        
        // Find country by dial code
        for (const [countryCode, country] of Object.entries(countryData)) {
            if (country.code === code) {
                return { code: countryCode, ...country };
            }
        }
        
        return null;
    }

    function updateCountryCodeDisplay(country) {
        if (country) {
            countryCodeDisplay.value = country.code;
            countryCodeInput.value = country.code;
            
            // Save the selection to localStorage for persistence
            localStorage.setItem('selectedCountryCode', country.code);
        }
    }

    function updatePhoneExample(country) {
        if (country) {
            exampleDiv.textContent = `e.g. ${country.code}${country.example || '555123456'}`;
        } else {
            exampleDiv.textContent = '';
        }
    }

    function validatePhone() {
        const phone = phoneInput.value.trim();
        const countryCode = countryCodeDisplay.value.trim();
        
        if (!phone || !countryCode) {
            feedbackDiv.innerHTML = '';
            feedbackDiv.className = 'phone-feedback';
            exampleDiv.textContent = '';
            isPhoneValid = false;
            updateSubmitButton();
            return;
        }

        // Detect country from country code
        const detectedCountry = detectCountryFromCode(countryCode);
        
        if (detectedCountry) {
            updateCountryCodeDisplay(detectedCountry);
            updatePhoneExample(detectedCountry);
        } else {
            exampleDiv.textContent = '';
        }

        // Show loading state
        feedbackDiv.innerHTML = '<span class="phone-feedback-icon">⏳</span> Validating...';
        feedbackDiv.className = 'phone-feedback';

        // Clear previous timeout
        clearTimeout(validationTimeout);
        
        // Validate after 500ms delay
        validationTimeout = setTimeout(() => {
            const fullPhone = `+${countryCode}${phone}`;
            fetch('/api/validate-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone: fullPhone,
                    country: detectedCountry ? detectedCountry.code : 'US'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    feedbackDiv.innerHTML = '<span class="phone-feedback-icon">✓</span> Valid number';
                    feedbackDiv.className = 'phone-feedback valid';
                    isPhoneValid = true;
                } else {
                    feedbackDiv.innerHTML = '<span class="phone-feedback-icon">✗</span> ' + (data.message || 'Invalid format');
                    feedbackDiv.className = 'phone-feedback invalid';
                    isPhoneValid = false;
                }
                updateSubmitButton();
            })
            .catch(error => {
                feedbackDiv.innerHTML = '<span class="phone-feedback-icon">✗</span> Validation failed';
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

    // Event listeners
    phoneInput.addEventListener('input', validatePhone);
    countryCodeDisplay.addEventListener('input', validatePhone); // Listen for changes in country code display

    // Initialize
    initializePhoneInput();

    // Initial validation if there's an old value
    if (phoneInput.value) {
        validatePhone();
    }
});
</script>
@endsection 