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
        border-radius: 8px;
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
        font-size: 14px;
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
        font-size: 14px;
        outline: none;
        background: white;
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
        background: transparent;
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
                            class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Enter your full name">
                        
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
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
                            class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                            placeholder="Enter your password">
                        
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password_confirmation') border-red-500 @enderror"
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
                    
                    <!-- Terms of Service -->
                    <div class="mb-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms_accepted" type="checkbox" name="terms_accepted" value="1" required
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 @error('terms_accepted') border-red-500 @enderror">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms_accepted" class="text-gray-700">
                                    I agree to the 
                                    <button type="button" onclick="document.getElementById('termsModal').classList.remove('hidden')" class="text-blue-600 hover:text-blue-800 underline">
                                        Terms of Service
                                    </button>
                                </label>
                                @error('terms_accepted')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Trial Information -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">30-Day Free Trial</h3>
                            </div>
                        </div>
                        <p class="text-sm text-blue-700">
                            Start your free trial today! No credit card required. Full access to all features for 30 days.
                        </p>
                    </div>
                    
                    <div>
                        <button type="submit" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
                            Start Free Trial
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
        // Enable button by default, only disable if phone validation explicitly fails
        if (phoneInput.value.trim() && !isPhoneValid) {
            submitBtn.disabled = true;
        } else {
            submitBtn.disabled = false;
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
    
    // Initialize submit button state
    updateSubmitButton();
    
    // Add form submission debugging
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', function(e) {
        // Prevent the form from submitting so we can see the debug info
        e.preventDefault();
        
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('Form submission attempted');
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        console.log('Submit button disabled:', submitBtn.disabled);
        console.log('Form action URL:', form.getAttribute('action'));
        console.log('Current page URL:', window.location.href);
        
        // Show form data
        const formData = new FormData(form);
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        if (submitBtn.disabled) {
            console.log('❌ Form submission prevented - submit button is disabled');
            return false;
        }
        
        console.log('✅ Form submission would proceed...');
        console.log('=== END DEBUG ===');
        
        // Now actually submit the form
        console.log('Submitting form now...');
        form.submit();
    });
});
</script>

<!-- Terms of Service Modal -->
<div id="termsModal" class="fixed inset-0 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Terms of Service</h3>
                <button type="button" onclick="document.getElementById('termsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="max-h-96 overflow-y-auto text-sm text-gray-700 leading-relaxed">
                <div class="prose prose-sm max-w-none">
                    <h2 class="text-lg font-semibold mb-3">MaintainXtra Terms of Service</h2>
                    <p class="text-xs text-gray-500 mb-4">Last updated: 10 August 2025</p>
                    
                    <p class="mb-4">These Terms of Service (the "Terms") govern access to and use of the MaintainXtra website at www.maintainxtra.com and related applications, platforms, and services (collectively, the "Service"). These Terms form a binding agreement between MaintainXtra ("we," "us," or "our") and the entity or person agreeing to these Terms ("Customer," "you," or "your"). If you use the Service on behalf of an organization, you represent that you have authority to bind that organization to these Terms.</p>
                    
                    <p class="mb-4 font-semibold">Summary (not legally binding): MaintainXtra is a monthly subscription SaaS built for property managers and vacation rental managers. Your plan auto-renews each month until cancelled. You own your data; we license the software. Please use the Service lawfully and responsibly.</p>
                    
                    <h3 class="font-semibold mb-2">1. The Service; Accounts</h3>
                    <p class="mb-2"><strong>1.1 Eligibility.</strong> You must be at least 18 years old and able to enter into contracts to use the Service.</p>
                    <p class="mb-2"><strong>1.2 Account Registration.</strong> To use the Service, you must create an account and provide accurate, complete information. You are responsible for maintaining the confidentiality of your login credentials and for all activities under your account.</p>
                    <p class="mb-4"><strong>1.3 Authorized Users.</strong> You may allow your employees or contractors ("Authorized Users") to access the Service under your account, and you are responsible for their compliance with these Terms.</p>
                    
                    <h3 class="font-semibold mb-2">2. Subscription Plans, Billing & Taxes</h3>
                    <p class="mb-2"><strong>2.1 Plans.</strong> The Service is offered on a monthly subscription basis. Current plan features and pricing are presented during checkout or in your billing settings (the "Plan").</p>
                    <p class="mb-2"><strong>2.2 Auto-Renewal.</strong> Subscriptions automatically renew month-to-month unless cancelled prior to the end of the current billing period.</p>
                    <p class="mb-2"><strong>2.3 Billing.</strong> You authorize us and our payment processor to charge your payment method for all subscription fees, applicable taxes, and any add-ons or overage fees. Fees are payable in advance for each monthly period.</p>
                    <p class="mb-4"><strong>2.4 Price Changes.</strong> We may change pricing or features by providing advance notice via the Service or email. Changes take effect on your next renewal unless otherwise stated.</p>
                    
                    <h3 class="font-semibold mb-2">3. Free Trial, Grace Period, Cancellations & Refunds</h3>
                    <p class="mb-2"><strong>3.1 30-Day Free Trial (no credit card).</strong> When you sign up with your name, email, and password, your trial begins immediately and ends 30 days later. During Day 0–30, you have full access to the features included in your Plan.</p>
                    <p class="mb-2"><strong>3.2 Post-Trial Grace Period (Days 31–37).</strong> From the day after your trial expires through Day 37, your account enters a 7-day grace period. On login you will be redirected to the payment screen with the notice: "Your free trial has ended. Subscribe now to keep your data."</p>
                    <p class="mb-2"><strong>3.3 Account Lock (from Day 38).</strong> If payment is not completed by Day 38 after trial expiration, your account is locked. Locked accounts cannot access the Service and will see: "Your trial has expired. Reactivate anytime to continue."</p>
                    <p class="mb-4"><strong>3.4 Cancellation (Paid Plans).</strong> You can cancel any paid subscription at any time via billing settings. Cancellation becomes effective at the end of the current billing period; you will retain access until then.</p>
                    
                    <h3 class="font-semibold mb-2">4. Access Rights; Acceptable Use</h3>
                    <p class="mb-2"><strong>4.1 License.</strong> Subject to these Terms and your payment of applicable fees, we grant you a limited, non-exclusive, non-transferable, revocable right to access and use the Service for your internal business purposes.</p>
                    <p class="mb-4"><strong>4.2 Restrictions.</strong> You will not (and will not permit anyone to): (a) copy, modify, or create derivative works of the Service; (b) reverse engineer, decompile, or attempt to extract source code; (c) resell, lease, or provide the Service to third parties as a service bureau; (d) access the Service for competitive benchmarking; or (e) use the Service in violation of law or these Terms.</p>
                    
                    <h3 class="font-semibold mb-2">5. Customer Data, Privacy & Retention</h3>
                    <p class="mb-2"><strong>5.1 Customer Data Ownership.</strong> You retain all right, title, and interest in and to data, content, files, and information submitted to the Service ("Customer Data").</p>
                    <p class="mb-2"><strong>5.2 Our Use of Customer Data.</strong> You grant us a worldwide, non-exclusive license to host, process, transmit, display, and otherwise use Customer Data to provide and maintain the Service; to prevent or address security, support, or technical issues; and as otherwise permitted by these Terms.</p>
                    <p class="mb-4"><strong>5.3 Privacy.</strong> Our collection and use of personal data is described in our Privacy Notice. You are responsible for providing notices and obtaining any required consents from your end users.</p>
                    
                    <h3 class="font-semibold mb-2">6. Warranties & Disclaimers</h3>
                    <p class="mb-4"><strong>6.1 Service Disclaimer.</strong> THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE," WITHOUT WARRANTIES OF ANY KIND, WHETHER EXPRESS, IMPLIED, STATUTORY, OR OTHERWISE, INCLUDING WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. WE DO NOT WARRANT THAT THE SERVICE WILL BE UNINTERRUPTED, ERROR-FREE, OR SECURE.</p>
                    
                    <h3 class="font-semibold mb-2">7. Limitation of Liability</h3>
                    <p class="mb-4">TO THE MAXIMUM EXTENT PERMITTED BY LAW: (a) NEITHER PARTY WILL BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, COVER, OR PUNITIVE DAMAGES, OR LOSS OF PROFITS, REVENUE, DATA, OR GOODWILL, EVEN IF ADVISED OF THE POSSIBILITY; AND (b) EACH PARTY'S TOTAL LIABILITY ARISING OUT OF OR RELATED TO THESE TERMS WILL NOT EXCEED THE AMOUNTS PAID OR PAYABLE BY YOU TO US FOR THE SERVICE IN THE TWELVE (12) MONTHS PRECEDING THE EVENT GIVING RISE TO LIABILITY.</p>
                    
                    <h3 class="font-semibold mb-2">8. Contact</h3>
                    <p class="mb-4">Questions about these Terms? Contact us at admin@maintainxtra.com</p>
                    
                    <p class="text-sm text-gray-600 mt-4">By using the Service, you acknowledge that you have read and agree to these Terms.</p>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="document.getElementById('termsModal').classList.add('hidden')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 