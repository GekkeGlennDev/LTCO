<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTOs\ExchangeRateInfo;
use App\Models\Currency;
use App\Services\CurrencyService;
use App\ValueObjects\Name;
use App\ValueObjects\Rate;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

#[Signature('app:fetch-currency-feed {--c|currency=}')]
#[Description('Fetch currency feeds')]
class FetchCurrencyFeed extends Command
{
    private const string CURRENCY_FEED_URL = 'https://www.floatrates.com/daily/%s.json';

    private readonly CurrencyService $currencyService;
    private readonly CarbonImmutable $today;

    public function handle(): int
    {
        $this->currencyService = app()->make(CurrencyService::class);

        $this->today = CarbonImmutable::today();
        $this->info('Fetching currency feeds for ' . $this->today->toDateString());

        $option = $this->option('currency');

        // When no option is given, process known currencies.
        if (!$option) {
            return $this->processKnownCurrencies();
        }

        try {
            return $this->processSingeItem(new Name($option));
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());
        }

        return self::FAILURE;
    }

    private function processKnownCurrencies(): int
    {
        // get available currencies.
        $noErrors = true;
        Currency::query()->chunkById(100, fn (Collection $chunk) => $chunk
            ->each(function (Currency $currency) use (&$noErrors) {
                try {
                    $this->processCurrency($currency);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    $noErrors = false;
                }
            })
        );

        return $noErrors ? self::SUCCESS : self::FAILURE;
    }

    private function processSingeItem(Name $name): int
    {
        if (!$this->validateCurrency($name)) {
            $this->warn('Invalid currency');
            return self::INVALID;
        }

        $currency = $this->currencyService->findOrCreate($name);
        $noErrors = true;

        try {
            $this->processCurrency($currency);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $noErrors = false;
        }

        return $noErrors ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @throws \Exception
     */
    private function processCurrency(Currency $currency): void
    {
        $feed = $this->fetchFeed($currency);

        if ($feed === null) {
            throw new \Exception('Feed not found');
        }

        $feed->each(fn (ExchangeRateInfo $exchangeRateInfo) => $this->processCurrencyExchangeRate(
            $currency,
            $exchangeRateInfo
        ));
    }

    private function processCurrencyExchangeRate(Currency $baseCurrency, ExchangeRateInfo $exchangeRateInfo): void
    {
        $targetCurrency = $this->currencyService->findOrCreate($exchangeRateInfo->currency);
        $this->currencyService->storeCurrencyExchangeRate(
            $baseCurrency,
            $targetCurrency,
            $exchangeRateInfo->rate,
            $this->today
        );

        $this->info(sprintf('Currency exchange rate stored for %s to %s', $baseCurrency->name, $targetCurrency->name));
    }

    private function fetchFeed(Currency $currency): ?Collection
    {
        $url = sprintf(self::CURRENCY_FEED_URL, $currency->name);

        try {
            $response = Http::get($url);
        } catch (ConnectionException $e) {
            $this->error((string)$e->getCode());
            return null;
        }

        if (!$response->successful()) {
            $this->error('Invalid response');
            return null;
        }

        $feed = Collection::empty();
        foreach ($response->json() as $currency => $data) {
            $feed->add(new ExchangeRateInfo(
                new Name($currency),
                new Rate($data['rate']),
            ));
        }

        $this->info('Feed fetched successfully');
        return $feed;
    }

    private function validateCurrency(Name $name): bool
    {
        $url = sprintf(self::CURRENCY_FEED_URL, $name->value);

        try {
            return Http::get($url)->successful();
        } catch (ConnectionException $e) {
            $this->error((string)$e->getCode());
        }
        return false;
    }
}
