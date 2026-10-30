<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleMapsLocationUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $host = strtolower((string) parse_url($value, PHP_URL_HOST));
        $path = (string) parse_url($value, PHP_URL_PATH);
        parse_str((string) parse_url($value, PHP_URL_QUERY), $query);

        $valid = parse_url($value, PHP_URL_SCHEME) === 'https'
            && (
                ($host === 'maps.app.goo.gl' && strlen($path) > 1)
                || ($host === 'goo.gl' && str_starts_with($path, '/maps/'))
                || (in_array($host, ['google.com', 'www.google.com', 'maps.google.com', 'google.co.id', 'www.google.co.id'], true)
                    && ($host === 'maps.google.com' || preg_match('#^/maps(?:/|$)#', $path)))
            )
            && ! preg_match('#/embed(?:/|$)#i', $path)
            && ($query['output'] ?? null) !== 'embed';

        if (! $valid) {
            $fail('Gunakan link lokasi Google Maps dari Bagikan > Salin link, bukan URL embed.');
        }
    }
}
