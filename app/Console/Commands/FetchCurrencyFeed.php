<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FetchCurrencyExchangeRates;
use App\Processors\CurrencyFeedProcessor;
use App\Services\CurrencyService;
use App\ValueObjects\Name;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Bus;
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
        Bus::dispatch(new FetchCurrencyExchangeRates());

        $this->info('Job to fetch currencies is being dispatched');

        return self::SUCCESS;
    }

    private function processSingeItem(Name $name): int
    {
        if (!$this->validateCurrency($name)) {
            $this->warn('Invalid currency');
            return self::INVALID;
        }

        $currency = $this->currencyService->findOrCreate($name);
        resolve(CurrencyFeedProcessor::class)->process($currency, $this->today);

        return self::SUCCESS;
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
