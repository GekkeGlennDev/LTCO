<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accepts a single IPv4/IPv6 address or a subnet in CIDR notation.
 */
class IpOrSubnet implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! self::isValid($value)) {
            $fail('The :attribute must be a valid IP address or subnet in CIDR notation (e.g. 192.168.1.0/24).');
        }
    }

    public static function isValid(string $value): bool
    {
        [$address, $prefix] = array_pad(explode('/', $value, 2), 2, null);

        if (filter_var($address, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        if ($prefix === null) {
            return true;
        }

        $maxPrefix = filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false ? 128 : 32;

        return ctype_digit($prefix) && (int) $prefix <= $maxPrefix;
    }
}
