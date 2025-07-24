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

    /* Enhanced Phone input styling */
    .phone-container {
        position: relative;
        display: flex;
        gap: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    
    .phone-container:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .country-dropdown {
        position: relative;
        width: 140px;
        background: #f9fafb;
        border-right: 1px solid #e5e7eb;
    }
    
    .country-select-button {
        width: 100%;
        padding: 12px 8px 12px 12px;
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        transition: background-color 0.2s ease;
    }
    
    .country-select-button:hover {
        background: #f3f4f6;
    }
    
    .country-flag {
        font-size: 16px;
        margin-right: 4px;
    }
    
    .country-code {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #1f2937;
    }
    
    .country-dropdown-arrow {
        font-size: 10px;
        color: #6b7280;
        transition: transform 0.2s ease;
    }
    
    .country-dropdown.open .country-dropdown-arrow {
        transform: rotate(180deg);
    }
    
    .country-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-height: 240px;
        overflow-y: auto;
        display: none !important;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.2s ease;
    }
    
    .country-options.show {
        display: block !important;
        opacity: 1;
        transform: translateY(0);
    }
    
    .country-search {
        padding: 8px 12px;
        border: none;
        border-bottom: 1px solid #e5e7eb;
        width: 100%;
        font-size: 13px;
        outline: none;
        background: #f9fafb;
    }
    
    .country-search:focus {
        background: white;
    }
    
    .country-option {
        padding: 10px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        font-size: 13px;
        transition: background-color 0.15s ease;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .country-option:hover {
        background: #f3f4f6;
    }
    
    .country-option:last-child {
        border-bottom: none;
    }
    
    .country-option-flag {
        font-size: 16px;
        margin-right: 8px;
        width: 20px;
    }
    
    .country-option-name {
        flex: 1;
        color: #374151;
        margin-right: 8px;
    }
    
    .country-option-code {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #6b7280;
        font-size: 12px;
    }
    
    .phone-input {
        flex: 1;
        border: none;
        padding: 12px 16px;
        font-size: 14px;
        outline: none;
        background: white;
    }
    
    .phone-input::placeholder {
        color: #9ca3af;
    }
    
    .phone-feedback {
        font-size: 12px;
        margin-top: 6px;
        transition: all 0.3s ease;
        min-height: 18px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .phone-feedback.invalid {
        color: #dc2626;
    }
    
    .phone-feedback.valid {
        color: #16a34a;
    }
    
    .phone-feedback-icon {
        font-size: 12px;
    }
    
    /* Scrollbar styling for country dropdown */
    .country-options::-webkit-scrollbar {
        width: 6px;
    }
    
    .country-options::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .country-options::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .country-options::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<div class="flex justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create an Account</h2>
                
                <form method="POST" action="{{ route('web.register.submit') }}" id="registerForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="appearance-none border rounded-md w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Enter your full name">
                        
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="appearance-none border rounded-md w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                            placeholder="Enter your email address">
                        
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 text-sm font-medium mb-2">Phone Number</label>
                        <div class="phone-container @error('phone') border-red-500 @enderror @error('country_code') border-red-500 @enderror">
                            <div class="country-dropdown" id="countryDropdown">
                                <button type="button" class="country-select-button" id="countrySelectButton">
                                    <div class="flex items-center">
                                        <span class="country-flag" id="selectedFlag">🇺🇸</span>
                                        <span class="country-code" id="selectedCode">+1</span>
                                    </div>
                                    <span class="country-dropdown-arrow">▼</span>
                                </button>
                                
                                <div class="country-options" id="countryOptions">
                                    <input type="text" class="country-search" id="countrySearch" placeholder="Search countries...">
                                    <div id="countryList">
                                        <!-- Countries will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', $userCountry) }}">
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                class="phone-input"
                                placeholder="Enter phone number">
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
                            class="appearance-none border rounded-md w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                            placeholder="Enter your password">
                        
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="appearance-none border rounded-md w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Confirm your password">
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
                        <button type="submit" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="text-center text-sm text-gray-600">
                    Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Sign in</a>
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
    const phoneInput = document.getElementById('phone');
    const countryCodeInput = document.getElementById('country_code');
    const feedbackDiv = document.getElementById('phone-feedback');
    const submitBtn = document.getElementById('submitBtn');
    const countryDropdown = document.getElementById('countryDropdown');
    const countrySelectButton = document.getElementById('countrySelectButton');
    const countryOptions = document.getElementById('countryOptions');
    const countrySearch = document.getElementById('countrySearch');
    const countryList = document.getElementById('countryList');
    const selectedFlag = document.getElementById('selectedFlag');
    const selectedCode = document.getElementById('selectedCode');

    let validationTimeout;
    let isPhoneValid = false;
    let filteredCountries = [];

    // Initialize countries
    function initializeCountries() {
        filteredCountries = Object.entries(countryData).map(([code, country]) => ({
            code,
            name: country.name,
            dialCode: country.code,
            flag: countryFlags[code] || '🏳️'
        }));
        
        renderCountryList();
        
        // Ensure dropdown is hidden initially
        countryOptions.classList.remove('show');
        countryDropdown.classList.remove('open');
        
        // Priority order for country selection:
        // 1. Old form value (from validation errors)
        // 2. User's previous selection (from session/localStorage)
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
        
        selectCountry(selectedCountry);
    }

    function renderCountryList() {
        countryList.innerHTML = '';
        filteredCountries.forEach(country => {
            const option = document.createElement('div');
            option.className = 'country-option';
            option.dataset.code = country.code;
            option.innerHTML = `
                <span class="country-option-flag">${country.flag}</span>
                <span class="country-option-name">${country.name}</span>
                <span class="country-option-code">${country.dialCode}</span>
            `;
            option.addEventListener('click', () => {
                selectCountry(country.code);
                closeDropdown();
            });
            countryList.appendChild(option);
        });
    }

    function selectCountry(countryCode) {
        const country = filteredCountries.find(c => c.code === countryCode);
        if (country) {
            selectedFlag.textContent = country.flag;
            selectedCode.textContent = country.dialCode;
            
            // Always update the hidden input
            countryCodeInput.value = countryCode;
            
            // Save the selection to localStorage for persistence
            localStorage.setItem('selectedCountryCode', countryCode);
            
            // Update placeholder
            phoneInput.placeholder = `Enter phone number`;
            
            // Validate phone if there's input
            if (phoneInput.value.trim()) {
                validatePhone();
            }
        }
    }

    function openDropdown() {
        countryDropdown.classList.add('open');
        countryOptions.classList.add('show');
        countrySearch.focus();
        countrySearch.value = '';
        filterCountries('');
    }

    function closeDropdown() {
        countryDropdown.classList.remove('open');
        countryOptions.classList.remove('show');
    }

    function filterCountries(searchTerm) {
        const filtered = Object.entries(countryData)
            .map(([code, country]) => ({
                code,
                name: country.name,
                dialCode: country.code,
                flag: countryFlags[code] || '🏳️'
            }))
            .filter(country => 
                country.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                country.dialCode.includes(searchTerm) ||
                country.code.toLowerCase().includes(searchTerm.toLowerCase())
            );
        
        filteredCountries = filtered;
        renderCountryList();
    }

    function validatePhone() {
        const phone = phoneInput.value.trim();
        const country = countryCodeInput.value;
        
        if (!phone) {
            feedbackDiv.innerHTML = '';
            feedbackDiv.className = 'phone-feedback';
            isPhoneValid = false;
            updateSubmitButton();
            return;
        }

        // Show loading state
        feedbackDiv.innerHTML = '<span class="phone-feedback-icon">⏳</span> Validating...';
        feedbackDiv.className = 'phone-feedback';

        // Clear previous timeout
        clearTimeout(validationTimeout);
        
        // Validate after 500ms delay
        validationTimeout = setTimeout(() => {
            // Construct the full phone number for API validation
            const fullPhone = country + phone;
            
            fetch('/api/validate-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone: fullPhone,
                    country: country
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    feedbackDiv.innerHTML = '<span class="phone-feedback-icon">✓</span> Valid phone number';
                    feedbackDiv.className = 'phone-feedback valid';
                    isPhoneValid = true;
                } else {
                    feedbackDiv.innerHTML = '<span class="phone-feedback-icon">✗</span> ' + (data.message || 'Invalid phone number format');
                    feedbackDiv.className = 'phone-feedback invalid';
                    isPhoneValid = false;
                }
                updateSubmitButton();
            })
            .catch(error => {
                feedbackDiv.innerHTML = '<span class="phone-feedback-icon">✗</span> Unable to validate phone number';
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
    countrySelectButton.addEventListener('click', (e) => {
        e.preventDefault();
        if (countryOptions.classList.contains('show')) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });

    countrySearch.addEventListener('input', (e) => {
        filterCountries(e.target.value);
    });

    phoneInput.addEventListener('input', validatePhone);

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!countryDropdown.contains(e.target)) {
            closeDropdown();
        }
    });

    // Initialize
    initializeCountries();

    // Initial validation if there's an old value
    if (phoneInput.value) {
        validatePhone();
    }
});
</script>
@endsection 