<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    protected $secretKey;
    protected $siteKey;

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key');
        $this->siteKey = config('services.recaptcha.site_key');
    }

    /**
     * Verify reCAPTCHA response
     */
    public function verify(string $response, string $remoteIp = null): bool
    {
        if (empty($this->secretKey) || empty($response)) {
            Log::warning('reCAPTCHA validation failed: Missing secret key or response');
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $response,
                'remoteip' => $remoteIp ?? request()->ip(),
            ]);

            $result = $response->json();

            if ($result['success']) {
                return true;
            }

            Log::warning('reCAPTCHA validation failed', [
                'errors' => $result['error-codes'] ?? [],
                'response' => $result
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA validation exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Get the site key
     */
    public function getSiteKey(): string
    {
        return $this->siteKey ?? '';
    }

    /**
     * Check if reCAPTCHA is enabled
     */
    public function isEnabled(): bool
    {
        return !empty($this->siteKey) && !empty($this->secretKey);
    }
} 