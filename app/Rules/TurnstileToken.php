<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class TurnstileToken implements ValidationRule
{
    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $enabled = config('turnstile.enabled');

        if (! is_bool($enabled) || ! $enabled) {
            return;
        }

        $token = is_string($value) ? trim($value) : '';

        if ($token === '') {
            $fail('Please complete the verification challenge.');

            return;
        }

        $secret = config('turnstile.secret_key');

        if (! is_string($secret) || $secret === '') {
            $fail('Turnstile is not configured for this environment.');

            return;
        }

        $cacheKey = 'turnstile-token:'.hash('sha256', $token);

        if (! Cache::add($cacheKey, true, now()->addMinutes(5))) {
            $fail('That verification token has already been used. Please try again.');

            return;
        }

        $timeout = config('turnstile.timeout');
        $verifyEndpoint = config('turnstile.verify_endpoint');

        if (! is_int($timeout) || ! is_string($verifyEndpoint) || $verifyEndpoint === '') {
            Cache::forget($cacheKey);
            $fail('Turnstile is not configured for this environment.');

            return;
        }

        $response = Http::asForm()
            ->connectTimeout(5)
            ->timeout($timeout)
            ->post($verifyEndpoint, [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

        /** @var array<string, mixed>|null $payload */
        $payload = $response->json();
        $success = is_array($payload) && ($payload['success'] ?? false) === true;

        if ($success) {
            return;
        }

        Cache::forget($cacheKey);
        $fail('Verification failed. Please try again.');
    }
}
