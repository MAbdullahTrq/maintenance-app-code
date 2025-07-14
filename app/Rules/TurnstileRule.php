<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\TurnstileService;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $turnstileService = new TurnstileService();
        
        // Skip validation if Turnstile is not enabled
        if (!$turnstileService->isEnabled()) {
            return;
        }
        
        // Validate Turnstile response
        if (!$turnstileService->verify($value)) {
            $fail('Please verify that you are human.');
        }
    }
}
