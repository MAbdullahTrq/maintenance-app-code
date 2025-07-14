<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\RecaptchaService;

class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $recaptchaService = new RecaptchaService();
        
        // Skip validation if reCAPTCHA is not enabled
        if (!$recaptchaService->isEnabled()) {
            return;
        }
        
        // Validate reCAPTCHA response
        if (!$recaptchaService->verify($value)) {
            $fail('Please verify that you are not a robot.');
        }
    }
}
