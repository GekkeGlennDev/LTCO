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
use Illuminate\Support\Carbon;
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

    /**
     * Store a complete feed with a few bulk queries instead of one query per exchange rate.
     *
     * @param Collection<int, ExchangeRateInfo> $feed
     */
    public function storeExchangeRates(Currency $baseCurrency, Collection $feed, CarbonImmutable $validOn): void
    {
        if ($feed->isEmpty()) {
            return;
        }

        $now = now();
        $names = $feed->map(fn (ExchangeRateInfo $info) => $info->currency->value);

        // Bulk inserts bypass model events, so flush the cache when new currencies were added.
        $inserted = Currency::query()->insertOrIgnore($names
            ->map(fn (string $name) => ['name' => $name, 'created_at' => $now, 'updated_at' => $now])
            ->all());

        if ($inserted > 0) {
            $this->flushCache();
        }

        $currencyIds = Currency::query()->whereIn('name', $names)->pluck('id', 'name');

        ExchangeRate::query()->upsert(
            values: $feed
                ->map(fn (ExchangeRateInfo $info) => [
                    'base_currency_id' => $baseCurrency->id,
                    'target_currency_id' => $currencyIds[$info->currency->value],
                    'rate' => $info->rate->value,
                    'valid_on' => $validOn->toDateString(),
                ])
                ->all(),
            uniqueBy: ['base_currency_id', 'target_currency_id', 'valid_on'],
            update: ['rate'],
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
        return Carbon::make($this->cache->get(CurrencyService::CACHE_KEY_FETCHED_AT))->toImmutable();
    }
}
