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
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                            class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                            placeholder="+1 555 123 4567">
                        <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', $userCountry) }}">
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
                    
                    <div>
                        <button type="submit" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
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
@endsection 