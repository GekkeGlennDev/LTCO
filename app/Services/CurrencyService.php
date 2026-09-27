<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ExchangeRateInfo;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\ValueObjects\Name;
use App\ValueObjects\Rate;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public const string CURRENCY_FEED_URL = 'https://www.floatrates.com/daily/%s.json';
    public const string CACHE_KEY_FETCHING = 'currency.fetching';
    public const string CACHE_KEY_FETCHED_AT = 'currency.last_fetched_at';
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

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function fetchFeed(Currency $currency): Collection
    {
        $url = sprintf(self::CURRENCY_FEED_URL, $currency->name);

        $response = Http::get($url)->throw();
        $feed = Collection::empty();

        foreach ($response->json() as $currency => $data) {
            $feed->add(new ExchangeRateInfo(
                new Name($currency),
                new Rate($data['rate']),
            ));
        }

        return $feed;
    }

    public function isFetching(): bool
    {
        return $this->cache->has(CurrencyService::CACHE_KEY_FETCHING);
    }

    public function getLastFetched(): ?CarbonImmutable
    {
        return $this->cache->get(CurrencyService::CACHE_KEY_FETCHED_AT);
    }
}
