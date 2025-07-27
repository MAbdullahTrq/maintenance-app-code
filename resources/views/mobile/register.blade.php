@extends('mobile.layout')

@section('title', 'Register')

@push('styles')
<style>
    /* Modern mobile form styling */
    .modern-form-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 32px 24px;
        width: 100%;
        max-width: 400px;
        position: relative;
        overflow: hidden;
    }
    
    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }
    
    .form-header {
        text-align: center;
        margin-bottom: 32px;
    }
    
    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        letter-spacing: -0.025em;
    }
    
    .form-subtitle {
        font-size: 16px;
        color: #6b7280;
        font-weight: 400;
    }
    
    .input-group {
        margin-bottom: 24px;
        position: relative;
    }
    
    .input-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        letter-spacing: 0.025em;
    }
    
    .modern-input {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 500;
        color: #1f2937;
        background: #f9fafb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }
    
    .modern-input:focus {
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .modern-input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }
    
    .modern-input.error {
        border-color: #ef4444;
        background: #fef2f2;
    }
    
    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .error-message::before {
        content: '⚠';
        font-size: 12px;
    }
    
    /* Phone input styling */
    .phone-container {
        position: relative;
    }
    
    .phone-input-group {
        display: flex;
        gap: 12px;
        align-items: stretch;
    }
    
    .country-select {
        flex-shrink: 0;
        width: 80px;
        padding: 16px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        background: #f9fafb;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
        text-align: center;
    }
    
    .country-select:focus {
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .phone-input {
        flex: 1;
        padding: 16px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 500;
        color: #1f2937;
        background: #f9fafb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }
    
    .phone-input:focus {
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .phone-feedback {
        margin-top: 8px;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .phone-feedback.valid {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    
    .phone-feedback.invalid {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .phone-feedback.loading {
        background: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }
    
    .phone-example {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
        font-style: italic;
    }
    
    /* Turnstile styling */
    .turnstile-wrapper {
        display: flex;
        justify-content: center;
        margin: 24px 0;
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }
    
    .cf-turnstile {
        transform: scale(0.9);
        transform-origin: center;
    }
    
    /* Submit button */
    .submit-button {
        width: 100%;
        padding: 16px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        margin-top: 8px;
    }
    
    .submit-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }
    
    .submit-button:active:not(:disabled) {
        transform: translateY(0);
    }
    
    .submit-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .submit-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .submit-button:hover::before {
        left: 100%;
    }
    
    /* Sign in link */
    .signin-link {
        text-align: center;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }
    
    .signin-text {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 8px;
    }
    
    .signin-button {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .signin-button:hover {
        color: #5a67d8;
        text-decoration: underline;
    }
    
    /* Loading animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .loading {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Responsive adjustments */
    @media (max-width: 480px) {
        .form-card {
            padding: 24px 20px;
            margin: 10px;
        }
        
        .form-title {
            font-size: 24px;
        }
        
        .modern-input,
        .phone-input,
        .country-select {
            padding: 14px 16px;
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="modern-form-container">
    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title">Create Account</h1>
            <p class="form-subtitle">Join MaintainXtra today</p>
        </div>
        
        <form method="POST" action="{{ route('register.submit') }}" id="mobileRegisterForm">
            @csrf
            
            <div class="input-group">
                <label for="name" class="input-label">Full Name</label>
                <input type="text" name="name" id="name" 
                    class="modern-input @error('name') error @enderror" 
                    placeholder="Enter your full name" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label for="email" class="input-label">Email Address</label>
                <input type="email" name="email" id="email" 
                    class="modern-input @error('email') error @enderror" 
                    placeholder="Enter your email address" 
                    value="{{ old('email') }}" 
                    required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label for="mobile_phone" class="input-label">Phone Number</label>
                <div class="phone-container">
                    <div class="phone-input-group">
                        <select id="mobile_country_code_input" class="country-select">
                            @foreach($countries as $code => $country)
                                <option value="{{ $country['code'] }}" 
                                    data-country="{{ $code }}"
                                    {{ old('country_code', $userCountry) == $code ? 'selected' : '' }}>
                                    {{ $country['code'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="tel" name="phone" id="mobile_phone" 
                            class="phone-input @error('phone') error @enderror"
                            placeholder="Enter phone number" 
                            value="{{ old('phone') }}" 
                            required>
                    </div>
                    <input type="hidden" name="country_code" id="mobile_country_code" value="{{ old('country_code', $userCountry) }}">
                    <div id="mobile-phone-feedback" class="phone-feedback"></div>
                    <div id="mobile-phone-example" class="phone-example"></div>
                </div>
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label for="password" class="input-label">Password</label>
                <input type="password" name="password" id="password" 
                    class="modern-input @error('password') error @enderror" 
                    placeholder="Create a strong password" 
                    required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label for="password_confirmation" class="input-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                    class="modern-input" 
                    placeholder="Confirm your password" 
                    required>
            </div>
            
            <!-- Cloudflare Turnstile -->
            <div class="turnstile-wrapper">
                <div class="cf-turnstile" 
                     data-sitekey="{{ config('services.turnstile.site_key') }}"
                     data-theme="light"
                     data-size="flexible"></div>
            </div>
            @error('cf-turnstile-response')
                <div class="error-message">{{ $message }}</div>
            @enderror
            
            <button type="submit" id="mobileSubmitBtn" class="submit-button">
                Create Account
            </button>
        </form>
        
        <div class="signin-link">
            <p class="signin-text">Already have an account?</p>
            <a href="{{ route('login') }}" class="signin-button">Sign in</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Country data
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
    const countrySelect = document.getElementById('mobile_country_code_input');
    const countryCodeInput = document.getElementById('mobile_country_code');
    const feedbackDiv = document.getElementById('mobile-phone-feedback');
    const exampleDiv = document.getElementById('mobile-phone-example');
    const submitBtn = document.getElementById('mobileSubmitBtn');

    let validationTimeout;
    let isPhoneValid = false;

    // Initialize phone input
    function initializePhoneInput() {
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
        
        // Set the country select value
        countrySelect.value = selectedCountry;
        countryCodeInput.value = selectedCountry;
        
        const country = countryData[selectedCountry];
        if (country) {
            phoneInput.placeholder = `Enter phone number`;
            updatePhoneExample(country);
            localStorage.setItem('selectedCountryCode', selectedCountry);
        }
    }

    function updatePhoneExample(country) {
        if (country) {
            exampleDiv.textContent = `Example: ${country.code}${country.example || '555123456'}`;
        } else {
            exampleDiv.textContent = '';
        }
    }

    function validatePhone() {
        const phone = phoneInput.value.trim();
        const countryCode = countrySelect.value;
        
        if (!phone || !countryCode) {
            feedbackDiv.innerHTML = '';
            feedbackDiv.className = 'phone-feedback';
            exampleDiv.textContent = '';
            isPhoneValid = false;
            updateSubmitButton();
            return;
        }

        const country = countryData[countryCode];
        if (country) {
            updatePhoneExample(country);
        }

        // Show loading state
        feedbackDiv.innerHTML = '<span>⏳</span> Validating...';
        feedbackDiv.className = 'phone-feedback loading';

        // Clear previous timeout
        clearTimeout(validationTimeout);
        
        // Validate after 500ms delay
        validationTimeout = setTimeout(() => {
            const fullPhone = `+${country.code}${phone}`;
            fetch('/api/validate-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone: fullPhone,
                    country: countryCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    feedbackDiv.innerHTML = '<span>✓</span> Valid phone number';
                    feedbackDiv.className = 'phone-feedback valid';
                    isPhoneValid = true;
                } else {
                    feedbackDiv.innerHTML = '<span>✗</span> ' + (data.message || 'Invalid format');
                    feedbackDiv.className = 'phone-feedback invalid';
                    isPhoneValid = false;
                }
                updateSubmitButton();
            })
            .catch(error => {
                feedbackDiv.innerHTML = '<span>✗</span> Validation failed';
                feedbackDiv.className = 'phone-feedback invalid';
                isPhoneValid = false;
                updateSubmitButton();
            });
        }, 500);
    }

    function updateSubmitButton() {
        const form = document.getElementById('mobileRegisterForm');
        const requiredFields = form.querySelectorAll('input[required]');
        let allFieldsFilled = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                allFieldsFilled = false;
            }
        });
        
        if (allFieldsFilled && isPhoneValid) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Event listeners
    phoneInput.addEventListener('input', validatePhone);
    countrySelect.addEventListener('change', function() {
        const selectedCountry = this.value;
        countryCodeInput.value = selectedCountry;
        
        const country = countryData[selectedCountry];
        if (country) {
            updatePhoneExample(country);
            localStorage.setItem('selectedCountryCode', selectedCountry);
        }
        
        validatePhone();
    });

    // Add input event listeners for all required fields
    const requiredInputs = document.querySelectorAll('input[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('input', updateSubmitButton);
    });

    // Initialize
    initializePhoneInput();
    
    // Initial validation if there's an old value
    if (phoneInput.value) {
        validatePhone();
    }
    
    // Initial button state
    updateSubmitButton();
});
</script>
@endsection 