<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\ValueObjects\Name;
use App\ValueObjects\Rate;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;

class CurrencyService
{
    private const string CACHE_KEY = 'currencies';

    public function __construct(private readonly Cache $cache)
    {
    }

    public function all(): Collection
    {
        return collect($this->cache->rememberForever(
            self::CACHE_KEY,
            fn () => Currency::query()->pluck('name')->all(),
        ));
    }

    public function flushCache(): void
    {
        $this->cache->forget(self::CACHE_KEY);
    }

    public function findOrCreate(Name $name): Currency
    {
        return Currency::query()->createOrFirst(['name' => $name->value]);
    }

    public function storeCurrencyExchangeRate(
        Currency $baseCurrency,
        Currency $targetCurrency,
        Rate $rate,
        CarbonImmutable $validOn
    ): ExchangeRate {
        return ExchangeRate::query()->createOrFirst(
            attributes: [
                'base_currency_id' => $baseCurrency->id,
                'target_currency_id' => $targetCurrency->id,
                'valid_on' => $validOn->toDateString(),
            ],
            values: [
                'rate' => $rate->value,
            ]
        );
    }
}
