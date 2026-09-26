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

    public function flushIpCache(): void
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
