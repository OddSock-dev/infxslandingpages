<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

class CrmPayloadRedactor
{
    /**
     * @param  array<int|string, mixed>|null  $payload
     * @return array<int|string, mixed>
     */
    public static function redactArray(?array $payload): array
    {
        if ($payload === null) {
            return [];
        }

        $redacted = [];

        foreach ($payload as $key => $value) {
            $redacted[$key] = self::redactValue((string) $key, $value);
        }

        return $redacted;
    }

    /**
     * @param  array<int|string, mixed>|null  $payload
     * @return array<string, string>
     */
    public static function flattenForDisplay(?array $payload): array
    {
        $flattened = [];
        self::flatten('', self::redactArray($payload), $flattened);
        ksort($flattened);

        return $flattened;
    }

    public static function redactField(string $field, ?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return match (true) {
            Str::contains(Str::lower($field), 'email') => self::redactEmail($value),
            Str::contains(Str::lower($field), ['phone', 'mobile']) => self::redactPhone($value),
            Str::contains(Str::lower($field), ['token', 'secret', 'password', 'authorization']) => '[redacted]',
            Str::contains(Str::lower($field), ['name', 'company']) => self::redactText($value),
            default => self::redactMessage($value),
        };
    }

    public static function redactMessage(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $redacted = preg_replace_callback(
            '/([A-Z0-9._%+\-]+)@([A-Z0-9.\-]+\.[A-Z]{2,})/iu',
            static fn (array $matches): string => self::redactEmail($matches[0]),
            $value,
        ) ?? $value;

        $redacted = preg_replace_callback(
            '/(?<!\w)(?:\+?\d[\d\s().-]{7,}\d)/u',
            static fn (array $matches): string => self::redactPhone($matches[0]),
            $redacted,
        ) ?? $redacted;

        return preg_replace('/\b[A-Za-z0-9_\-]{24,}\b/u', '[redacted]', $redacted) ?? $redacted;
    }

    private static function redactEmail(string $value): string
    {
        if (! str_contains($value, '@')) {
            return '[redacted]';
        }

        [$local, $domain] = explode('@', $value, 2);
        $visible = mb_substr($local, 0, 1);

        return sprintf('%s***@%s', $visible === '' ? '*' : $visible, $domain);
    }

    private static function redactPhone(string $value): string
    {
        $digits = preg_replace('/\D+/u', '', $value);

        if (! is_string($digits) || $digits === '') {
            return '[redacted]';
        }

        $suffix = substr($digits, -4);

        return str_repeat('*', max(strlen($digits) - 4, 4)).$suffix;
    }

    private static function redactText(string $value): string
    {
        $parts = preg_split('/\s+/u', trim($value)) ?: [];

        if ($parts === []) {
            return '[redacted]';
        }

        $redacted = array_map(static function (string $part): string {
            $visible = mb_substr($part, 0, 1);

            return ($visible === '' ? '*' : $visible).'***';
        }, $parts);

        return implode(' ', $redacted);
    }

    private static function redactValue(string $key, mixed $value): mixed
    {
        if (is_array($value)) {
            return self::redactArray($value);
        }

        if (! is_string($value)) {
            return $value;
        }

        return self::redactField($key, $value);
    }

    /**
     * @param  array<int|string, mixed>  $payload
     * @param  array<string, string>  $flattened
     */
    private static function flatten(string $prefix, array $payload, array &$flattened): void
    {
        foreach ($payload as $key => $value) {
            $path = $prefix === '' ? (string) $key : sprintf('%s.%s', $prefix, $key);

            if (is_array($value)) {
                if ($value === []) {
                    $flattened[$path] = '[]';

                    continue;
                }

                self::flatten($path, $value, $flattened);

                continue;
            }

            $flattened[$path] = self::stringify($value);
        }
    }

    private static function stringify(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? 'true' : 'false',
            $value === null => 'null',
            is_scalar($value) => (string) $value,
            default => json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '[unavailable]',
        };
    }
}
