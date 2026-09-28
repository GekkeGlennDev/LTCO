<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AllowedIp;
use Illuminate\Contracts\Cache\Repository as Cache;
use Symfony\Component\HttpFoundation\IpUtils;

class IpWhitelistService
{
    private const string CACHE_KEY = 'ip-whitelist.ranges';

    public function __construct(private readonly Cache $cache)
    {
    }

    public function allows(?string $ip): bool
    {
        if ($ip === null) {
            return false;
        }

        return IpUtils::checkIp($ip, $this->ranges());
    }

    /**
     * Determines if the given IP is allowed based on the allowed ranges,
     * excluding a specific range.
     *
     * @param string|null $ip The IP address to check.
     * @param AllowedIp $excluded The excluded range that should not be considered.
     *
     * @return bool True if the IP is allowed, false otherwise.
     */
    public function allowsWithout(?string $ip, AllowedIp $excluded): bool
    {
        if ($ip === null) {
            return false;
        }

        $ranges = array_values(array_diff($this->ranges(), [$excluded->range]));

        return IpUtils::checkIp($ip, $ranges);
    }

    public function flushCache(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    private function ranges(): array
    {
        return $this->cache->rememberForever(
            self::CACHE_KEY,
            fn () => AllowedIp::query()->pluck('range')->all(),
        );
    }
}
