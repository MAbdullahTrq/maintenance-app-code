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
        border-radius: 8px;
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
        padding: 12px 8px 12px 16px;
        font-size: 14px;
        outline: none;
        background: transparent;
        font-weight: 600;
        color: #374151;
        border-right: 1px solid #d1d5db;
        text-align: center;
    }
    
    .phone-country-code::placeholder {
        color: #9ca3af;
    }
    
    .phone-number-input {
        flex: 1;
        border: none;
        padding: 12px 16px;
        font-size: 14px;
        outline: none;
        background: transparent;
    }
    
    .phone-number-input::placeholder {
        color: #9ca3af;
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
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                        placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" name="email" id="email" 
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" 
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
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" 
                        placeholder="Password" required>
                    @error('password')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
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
                
                <!-- Terms of Service -->
                <div class="mb-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="mobile_terms_accepted" type="checkbox" name="terms_accepted" value="1" required
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 @error('terms_accepted') border-red-500 @enderror">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="mobile_terms_accepted" class="text-gray-700">
                                I agree to the 
                                <button type="button" onclick="document.getElementById('mobileTermsModal').classList.remove('hidden')" class="text-blue-600 hover:text-blue-800 underline">
                                    Terms of Service
                                </button>
                            </label>
                            @error('terms_accepted')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Trial Information -->
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-2">
                            <h3 class="text-xs font-medium text-blue-800">30-Day Free Trial</h3>
                        </div>
                    </div>
                    <p class="text-xs text-blue-700">
                        Start your free trial today! No credit card required.
                    </p>
                </div>
                
                <button type="submit" id="mobileSubmitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold text-sm transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    Start Free Trial
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
    
    // Add form submission debugging
    const form = document.getElementById('mobileRegisterForm');
    form.addEventListener('submit', function(e) {
        // Prevent the form from submitting so we can see the debug info
        e.preventDefault();
        
        console.log('=== MOBILE FORM SUBMISSION DEBUG ===');
        console.log('Mobile form submission attempted');
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        console.log('Submit button disabled:', mobileSubmitBtn.disabled);
        console.log('Form action URL:', form.getAttribute('action'));
        console.log('Current page URL:', window.location.href);
        
        // Show form data
        const formData = new FormData(form);
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        if (mobileSubmitBtn.disabled) {
            console.log('❌ Form submission prevented - submit button is disabled');
            return false;
        }
        
        console.log('✅ Form submission would proceed...');
        console.log('=== END DEBUG ===');
        
        // Now actually submit the form
        console.log('Submitting mobile form now...');
        form.submit();
    });

    // Initial validation if there's an old value
    if (phoneInput.value) {
        validatePhone();
    }
});
</script>

<!-- Mobile Terms of Service Modal -->
<div id="mobileTermsModal" class="hidden fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white p-4 rounded shadow max-w-md w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Terms of Service</h3>
            <button type="button" onclick="document.getElementById('mobileTermsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="text-sm text-gray-700 leading-relaxed">
            <h4 class="text-lg font-bold mb-2">MaintainXtra Terms of Service</h4>
            <p class="text-xs text-gray-500 mb-4">Last updated: 10 August 2025</p>
            
            <p class="mb-4">These Terms of Service govern access to and use of the MaintainXtra website and services. By using the Service, you agree to these Terms.</p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <p class="font-semibold text-sm">Summary:</p>
                <p class="text-sm">MaintainXtra is a monthly subscription SaaS for property managers. Your plan auto-renews each month until cancelled. You own your data; we license the software.</p>
            </div>
            
            <h5 class="font-bold mb-2 text-base">1. The Service; Accounts</h5>
            <p class="mb-2"><strong>1.1 Eligibility.</strong> You must be at least 18 years old and able to enter into contracts to use the Service.</p>
            <p class="mb-2"><strong>1.2 Account Registration.</strong> To use the Service, you must create an account and provide accurate, complete information. You are responsible for maintaining the confidentiality of your login credentials and for all activities under your account.</p>
            <p class="mb-3"><strong>1.3 Authorized Users.</strong> You may allow your employees or contractors ("Authorized Users") to access the Service under your account, and you are responsible for their compliance with these Terms.</p>
            
            <h5 class="font-bold mb-2 text-base">2. Subscription Plans, Billing & Taxes</h5>
            <p class="mb-2"><strong>2.1 Plans.</strong> The Service is offered on a monthly subscription basis. Current plan features and pricing are presented during checkout or in your billing settings (the "Plan").</p>
            <p class="mb-2"><strong>2.2 Auto-Renewal.</strong> Subscriptions automatically renew month-to-month unless cancelled prior to the end of the current billing period.</p>
            <p class="mb-2"><strong>2.3 Billing.</strong> You authorize us and our payment processor to charge your payment method for all subscription fees, applicable taxes, and any add-ons or overage fees. Fees are payable in advance for each monthly period.</p>
            <p class="mb-2"><strong>2.4 Price Changes.</strong> We may change pricing or features by providing advance notice via the Service or email. Changes take effect on your next renewal unless otherwise stated.</p>
            <p class="mb-2"><strong>2.5 Taxes.</strong> Fees are exclusive of taxes, levies, duties, or similar governmental assessments (collectively, "Taxes"). You are responsible for all Taxes associated with your purchase, except for taxes based on our net income.</p>
            <p class="mb-3"><strong>2.6 Upgrades & Downgrades.</strong> Upgrades take effect immediately and may be prorated for the remainder of the current period. Downgrades take effect at the next renewal and may impact features, limits, or storage.</p>
            
            <h5 class="font-bold mb-2 text-base">3. Free Trial, Grace Period, Cancellations & Refunds</h5>
            <p class="mb-2"><strong>3.1 30-Day Free Trial (no credit card).</strong> When you sign up with your name, email, and password, your trial begins immediately (trial_started_at = the time of registration) and ends 30 days later (trial_expires_at = trial_started_at + 30 days). During Day 0–30, you have full access to the features included in your Plan.</p>
            <p class="mb-2"><strong>3.2 Post-Trial Grace Period (Days 31–37).</strong> From the day after your trial expires through Day 37, your account enters a 7-day grace period. On login you will be redirected to the payment screen with the notice: "Your free trial has ended. Subscribe now to keep your data."</p>
            <p class="mb-2"><strong>3.3 Account Lock (from Day 38).</strong> If payment is not completed by Day 38 after trial expiration, your account is locked. Locked accounts cannot access the Service and will see: "Your trial has expired. Reactivate anytime to continue."</p>
            <p class="mb-2"><strong>3.4 Cancellation (Paid Plans).</strong> You can cancel any paid subscription at any time via billing settings. Cancellation becomes effective at the end of the current billing period; you will retain access until then.</p>
            <p class="mb-2"><strong>3.5 Refunds.</strong> Except where required by law or expressly stated otherwise, all fees are non-refundable and non-creditable, including for partial periods and unused features.</p>
            <p class="mb-3"><strong>3.6 Trials Are As-Is.</strong> During the free trial, the Service is provided "as is" with no warranties or commitments, and features or limits may change.</p>
            
            <h5 class="font-bold mb-2 text-base">4. Access Rights; Acceptable Use</h5>
            <p class="mb-2"><strong>4.1 License.</strong> Subject to these Terms and your payment of applicable fees, we grant you a limited, non-exclusive, non-transferable, revocable right to access and use the Service for your internal business purposes.</p>
            <p class="mb-2"><strong>4.2 Restrictions.</strong> You will not (and will not permit anyone to): (a) copy, modify, or create derivative works of the Service; (b) reverse engineer, decompile, or attempt to extract source code; (c) resell, lease, or provide the Service to third parties as a service bureau; (d) access the Service for competitive benchmarking; or (e) use the Service in violation of law or these Terms.</p>
            <p class="mb-3"><strong>4.3 Acceptable Use.</strong> You agree not to upload or transmit any content that is unlawful, infringing, defamatory, harassing, abusive, deceptive, malware, or that violates privacy or intellectual property rights; not to interfere with the security or operation of the Service; and not to attempt unauthorized access to accounts or systems.</p>
            
            <h5 class="font-bold mb-2 text-base">5. Customer Data, Privacy & Retention (Trials)</h5>
            <p class="mb-2"><strong>5.1 Customer Data Ownership.</strong> You retain all right, title, and interest in and to data, content, files, and information submitted to the Service ("Customer Data").</p>
            <p class="mb-2"><strong>5.2 Our Use of Customer Data.</strong> You grant us a worldwide, non-exclusive license to host, process, transmit, display, and otherwise use Customer Data to provide and maintain the Service; to prevent or address security, support, or technical issues; and as otherwise permitted by these Terms.</p>
            <p class="mb-2"><strong>5.3 Privacy.</strong> Our collection and use of personal data is described in our Privacy Notice. You are responsible for providing notices and obtaining any required consents from your end users.</p>
            <p class="mb-2"><strong>5.4 Security.</strong> We implement commercially reasonable safeguards designed to protect Customer Data. However, no method of transmission or storage is completely secure.</p>
            <p class="mb-2"><strong>5.5 Data Retention for Trials.</strong> If your free trial ends without conversion to a paid subscription, we will retain your Customer Data for 90 days from the trial_expires_at timestamp (the "Trial Retention Period"). During this period you may reactivate by subscribing to regain access.</p>
            <p class="mb-2"><strong>5.6 Reminder Emails During Retention.</strong> We may send up to three reminder emails about account status and data retention around Day 37, Day 60, and Day 85 following trial expiration (timing may vary). Example subject lines include: "Still need maintenance support? Your data is safe (for now)", "We're holding your account — ready when you are.", and "Last chance to save your account before it's deleted".</p>
            <p class="mb-2"><strong>5.7 Deletion After Retention.</strong> After the Trial Retention Period (i.e., after 90 days from trial expiration), we will delete your user record and associated Customer Data from active systems and schedule removal from backups in the ordinary course of business, except where retention is required by law. We may retain non-personal or aggregated data that does not identify you to improve and secure the Service.</p>
            <p class="mb-2"><strong>5.8 Email for Offers After Deletion (GHL).</strong> Subject to your consent and applicable law, after deletion of your account records we may retain or transfer your email address only to our customer relationship platform (GoHighLevel, "GHL") to send occasional offers, seasonal campaigns, or reactivation incentives. You can withdraw consent or unsubscribe at any time. Details are provided in our Privacy Notice and, if applicable, our Data Processing Addendum ("DPA").</p>
            <p class="mb-2"><strong>5.9 Data Export.</strong> Prior to deletion, and while in the grace period or Trial Retention Period, you may request an export of your Customer Data. We may require reasonable verification to process export requests.</p>
            <p class="mb-3"><strong>5.10 Data Location & Transfers.</strong> We may process and store data in locations where we or our subprocessors operate. Where required, we will implement appropriate transfer mechanisms.</p>
            
            <h5 class="font-bold mb-2 text-base">6. Third-Party Services & Integrations</h5>
            <p class="mb-3">The Service may interoperate with third-party products or services ("Third-Party Services"). Your use of Third-Party Services is subject to their terms and privacy policies. We are not responsible for Third-Party Services and disclaim all liability arising from them.</p>
            
            <h5 class="font-bold mb-2 text-base">7. Intellectual Property; Feedback</h5>
            <p class="mb-2"><strong>7.1 Our IP.</strong> We and our licensors own all right, title, and interest in and to the Service, including software, interfaces, designs, templates, know-how, and documentation. No rights are granted except as expressly stated in these Terms.</p>
            <p class="mb-3"><strong>7.2 Feedback.</strong> If you provide feedback, suggestions, or ideas ("Feedback"), you grant us a perpetual, irrevocable, worldwide, royalty-free license to use the Feedback without restriction.</p>
            
            <h5 class="font-bold mb-2 text-base">8. Beta, Early Access & Free Features</h5>
            <p class="mb-3">We may offer features identified as beta, preview, or early access ("Beta Features"). Beta Features may be unreliable or change at any time, are provided "as is," and are excluded from any warranties or service commitments.</p>
            
            <h5 class="font-bold mb-2 text-base">9. Availability, Support & Maintenance</h5>
            <p class="mb-2"><strong>9.1 Availability.</strong> We aim to keep the Service available 24/7, excluding planned maintenance and events beyond our reasonable control (see Section 15). We may update or modify the Service at any time.</p>
            <p class="mb-2"><strong>9.2 Support.</strong> We provide reasonable email or in-app support during business hours, excluding public holidays. Support scope may vary by Plan.</p>
            <p class="mb-3"><strong>9.3 Maintenance Windows.</strong> We may suspend access temporarily for maintenance or updates and will endeavor to schedule outside of peak hours and provide notice when feasible.</p>
            
            <h5 class="font-bold mb-2 text-base">10. Suspension & Termination</h5>
            <p class="mb-2"><strong>10.1 Suspension.</strong> We may suspend or limit the Service if: (a) you breach these Terms (including failure to pay); (b) your use poses a security risk; (c) your use could adversely impact the Service or others; (d) the trial or grace period has ended without payment; or (e) we are required by law.</p>
            <p class="mb-2"><strong>10.2 Grace Period & Lock.</strong> Following trial expiration, your account will be placed in a 7-day grace period (Days 31–37). If no subscription is purchased by Day 38, the account will be locked until payment is made.</p>
            <p class="mb-2"><strong>10.3 Termination by You.</strong> You may terminate at any time by cancelling your subscription. Termination is effective at the end of the current billing period.</p>
            <p class="mb-2"><strong>10.4 Termination by Us.</strong> We may terminate these Terms for cause if you materially breach and fail to cure within 10 days of notice, or for convenience upon 30 days' notice (with a pro rata refund for prepaid, unused fees if we terminate for convenience).</p>
            <p class="mb-3"><strong>10.5 Effect of Termination.</strong> Upon termination or expiration, your right to access the Service ends. We will make Customer Data export available for 30 days following termination of a paid subscription, and for the Trial Retention Period following a trial, after which we may delete or anonymize Customer Data, except as required by law.</p>
            
            <h5 class="font-bold mb-2 text-base">11. Warranties & Disclaimers</h5>
            <p class="mb-2"><strong>11.1 Mutual Warranties.</strong> Each party represents that it has the right and authority to enter into these Terms.</p>
            <p class="mb-3"><strong>11.2 Service Disclaimer.</strong> THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE," WITHOUT WARRANTIES OF ANY KIND, WHETHER EXPRESS, IMPLIED, STATUTORY, OR OTHERWISE, INCLUDING WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. WE DO NOT WARRANT THAT THE SERVICE WILL BE UNINTERRUPTED, ERROR-FREE, OR SECURE.</p>
            
            <h5 class="font-bold mb-2 text-base">12. Limitation of Liability</h5>
            <p class="mb-2">TO THE MAXIMUM EXTENT PERMITTED BY LAW: (a) NEITHER PARTY WILL BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, COVER, OR PUNITIVE DAMAGES, OR LOSS OF PROFITS, REVENUE, DATA, OR GOODWILL, EVEN IF ADVISED OF THE POSSIBILITY; AND (b) EACH PARTY'S TOTAL LIABILITY ARISING OUT OF OR RELATED TO THESE TERMS WILL NOT EXCEED THE AMOUNTS PAID OR PAYABLE BY YOU TO US FOR THE SERVICE IN THE TWELVE (12) MONTHS PRECEDING THE EVENT GIVING RISE TO LIABILITY.</p>
            <p class="mb-3 text-xs">Some jurisdictions do not allow certain limitations, so the above may not apply to you to the extent prohibited by law.</p>
            
            <h5 class="font-bold mb-2 text-base">13. Indemnification</h5>
            <p class="mb-3">You will defend, indemnify, and hold us and our affiliates, officers, directors, employees, and agents harmless from and against any claims, damages, liabilities, costs, and expenses (including reasonable attorneys' fees) arising from: (a) your Customer Data; (b) your use of the Service in violation of these Terms or law; or (c) your infringement or misappropriation of any third-party rights.</p>
            
            <h5 class="font-bold mb-2 text-base">14. Confidentiality</h5>
            <p class="mb-3">Each party will protect the other party's non-public information marked or reasonably understood to be confidential ("Confidential Information"). The receiving party will use Confidential Information solely to perform under these Terms and will protect it using reasonable care. Exceptions apply for information that is public, independently developed, or rightfully received without confidentiality obligations. If compelled by law to disclose, the receiving party will provide notice (if legally permitted) and cooperate to seek protective measures.</p>
            
            <h5 class="font-bold mb-2 text-base">15. Force Majeure</h5>
            <p class="mb-3">Neither party is liable for delays or failures due to events beyond reasonable control, including acts of God, natural disasters, war, terrorism, labor disputes, government actions, internet or utility failures, or third-party service provider outages.</p>
            
            <h5 class="font-bold mb-2 text-base">16. Publicity; Marks</h5>
            <p class="mb-3">With your consent (which may be given by email or in-app), we may identify you as a customer and use your name and logo on our website and marketing materials. You may revoke consent at any time by notifying us.</p>
            
            <h5 class="font-bold mb-2 text-base">17. Modifications to the Terms</h5>
            <p class="mb-3">We may update these Terms from time to time. If we make material changes, we will provide notice through the Service or via email. Changes become effective upon posting or on the stated effective date. If you continue using the Service after the effective date, you accept the revised Terms.</p>
            
            <h5 class="font-bold mb-2 text-base">18. Governing Law; Dispute Resolution</h5>
            <p class="mb-2">These Terms are governed by the laws of [JURISDICTION: e.g., Italy / State and Country], without regard to conflict of laws rules. The parties will submit to the exclusive jurisdiction and venue of the courts located in [CITY, COUNTRY/STATE]. The United Nations Convention on Contracts for the International Sale of Goods does not apply.</p>
            <p class="mb-3">If required by law, you may have rights to bring claims in your local courts or under your local consumer protection laws.</p>
            
            <h5 class="font-bold mb-2 text-base">19. Notices</h5>
            <p class="mb-3">Notices must be in writing and will be deemed given when: (a) delivered personally; (b) sent by certified or registered mail; (c) sent by a nationally recognized courier; or (d) sent by email to the addresses on file. Our contact for legal notices: [LEGAL EMAIL ADDRESS].</p>
            
            <h5 class="font-bold mb-2 text-base">20. General</h5>
            <p class="mb-2"><strong>20.1 Assignment.</strong> You may not assign these Terms without our prior written consent; we may assign to an affiliate or in connection with a merger, acquisition, or sale of assets.</p>
            <p class="mb-2"><strong>20.2 Entire Agreement.</strong> These Terms, together with any order forms, DPA, and policies referenced herein, constitute the entire agreement and supersede prior agreements regarding the Service.</p>
            <p class="mb-2"><strong>20.3 Severability; Waiver.</strong> If any provision is found unenforceable, it will be limited or eliminated to the minimum extent necessary. No waiver of any term is a waiver of any other term.</p>
            <p class="mb-3"><strong>20.4 No Third-Party Beneficiaries.</strong> There are no third-party beneficiaries to these Terms.</p>
            
            <h5 class="font-bold mb-2 text-base">21. Contact</h5>
            <p class="mb-3">Questions about these Terms? Contact us at admin@maintainxtra.com</p>
            
            <h5 class="font-bold mb-2 text-base">22. Account Communications & Marketing (Reminders and GHL)</h5>
            <p class="mb-2"><strong>22.1 Transactional Reminders.</strong> We may send account and service reminders related to your trial, grace period, and Trial Retention Period. As guidance, we may send up to three reminders around Day 37, Day 60, and Day 85 after trial expiration. Timing, content, and subject lines may vary.</p>
            <p class="mb-2"><strong>22.2 Marketing & CRM (GHL).</strong> With your consent and as permitted by law, after deletion of your account records we may retain or move your email address to our customer relationship platform (GoHighLevel, "GHL") to send occasional offers, seasonal campaigns, or reactivation incentives. You can withdraw consent or unsubscribe at any time via the link in our emails or by contacting support.</p>
            <p class="mb-3"><strong>22.3 Opt-Out.</strong> You may opt out of non-essential communications at any time. We will continue to send transactional communications necessary to administer your account (e.g., billing receipts, critical service notices).</p>
            
            <h5 class="font-bold mb-2 text-base">Optional Service-Specific Addendum (Property & Vacation Rental Management)</h5>
            <ul class="list-disc list-inside mb-3 space-y-1 text-xs">
                <li><strong>Tenant/Guest Data.</strong> You are responsible for the accuracy and lawfulness of tenant and guest information entered into the Service and for complying with applicable housing, tenant privacy, and hospitality laws.</li>
                <li><strong>Vendors/Service Providers.</strong> You are responsible for vetting and managing third-party vendors. The Service facilitates communication and work orders but does not supervise or guarantee vendor performance.</li>
                <li><strong>Compliance.</strong> You are solely responsible for compliance with local regulations, including data protection, consumer, housing, safety, and record-keeping rules applicable to your operations.</li>
            </ul>
            
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-4">
                <p class="text-sm font-semibold text-center">By using the Service, you acknowledge that you have read and agree to these Terms.</p>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" onclick="document.getElementById('mobileTermsModal').classList.add('hidden')" class="px-4 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                Close
            </button>
        </div>
    </div>
</div>
@endsection 