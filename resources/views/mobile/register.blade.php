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

    /* Enhanced Phone input styling for mobile */
    .phone-container {
        position: relative;
        display: flex;
        gap: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.2s ease;
        background: white;
    }
    
    .phone-container:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .country-dropdown {
        position: relative;
        width: 120px;
        background: #f9fafb;
        border-right: 1px solid #e5e7eb;
    }
    
    .country-select-button {
        width: 100%;
        padding: 14px 6px 14px 10px;
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        color: #374151;
        transition: background-color 0.2s ease;
    }
    
    .country-select-button:hover {
        background: #f3f4f6;
    }
    
    .country-flag {
        font-size: 14px;
        margin-right: 3px;
    }
    
    .country-code {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #1f2937;
        font-size: 11px;
    }
    
    .country-dropdown-arrow {
        font-size: 8px;
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
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-height: 200px;
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
        padding: 8px 10px;
        border: none;
        border-bottom: 1px solid #e5e7eb;
        width: 100%;
        font-size: 12px;
        outline: none;
        background: #f9fafb;
    }
    
    .country-search:focus {
        background: white;
    }
    
    .country-option {
        padding: 8px 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        font-size: 12px;
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
        font-size: 14px;
        margin-right: 6px;
        width: 16px;
    }
    
    .country-option-name {
        flex: 1;
        color: #374151;
        margin-right: 6px;
        font-size: 11px;
    }
    
    .country-option-code {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #6b7280;
        font-size: 10px;
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
        margin-top: 6px;
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
    
    /* Scrollbar styling for mobile country dropdown */
    .country-options::-webkit-scrollbar {
        width: 4px;
    }
    
    .country-options::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .country-options::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }
    
    .country-options::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
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
                    <div class="phone-container @error('phone') border-red-500 @enderror @error('country_code') border-red-500 @enderror">
                        <div class="country-dropdown" id="mobileCountryDropdown">
                            <button type="button" class="country-select-button" id="mobileCountrySelectButton">
                                <div class="flex items-center">
                                    <span class="country-flag" id="mobileSelectedFlag">🇺🇸</span>
                                    <span class="country-code" id="mobileSelectedCode">+1</span>
                                </div>
                                <span class="country-dropdown-arrow">▼</span>
                            </button>
                            
                            <div class="country-options" id="mobileCountryOptions">
                                <input type="text" class="country-search" id="mobileCountrySearch" placeholder="Search countries...">
                                <div id="mobileCountryList">
                                    <!-- Countries will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="country_code" id="mobile_country_code" value="{{ old('country_code', $userCountry) }}">
                        <input type="tel" name="phone" id="mobile_phone" 
                            class="phone-input"
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
    const feedbackDiv = document.getElementById('mobile-phone-feedback');
    const submitBtn = document.getElementById('mobileSubmitBtn');
    const countryDropdown = document.getElementById('mobileCountryDropdown');
    const countrySelectButton = document.getElementById('mobileCountrySelectButton');
    const countryOptions = document.getElementById('mobileCountryOptions');
    const countrySearch = document.getElementById('mobileCountrySearch');
    const countryList = document.getElementById('mobileCountryList');
    const selectedFlag = document.getElementById('mobileSelectedFlag');
    const selectedCode = document.getElementById('mobileSelectedCode');

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
        
        // Set initial country
        const initialCountry = '{{ $userCountry }}' || 'US';
        selectCountry(initialCountry);
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
            countryCodeInput.value = countryCode;
            
            // Update placeholder
            phoneInput.placeholder = `Phone number`;
            
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