<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Processors\CurrencyFeedProcessor;
use App\Services\CurrencyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FetchCurrencyExchangeRates implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $this->startFetching();
        $currencyValidOnDate = Carbon::today();

        Currency::query()->chunkById(100, fn (Collection $chunk) => $chunk
            ->each(fn (Currency $currency) => resolve(CurrencyFeedProcessor::class)
                ->process($currency, $currencyValidOnDate->toImmutable()))
        );

        $this->stopFetching();
        $this->storeLastFetchedAt();
    }

    private function startFetching(): void
    {
        Cache::put(CurrencyService::CACHE_KEY_FETCHING, true, now()->addHour());
    }

    private function stopFetching(): void
    {
        Cache::forget(CurrencyService::CACHE_KEY_FETCHING);
    }

    private function storeLastFetchedAt(): void
    {
        Cache::put(CurrencyService::CACHE_KEY_FETCHED_AT, now());
    }
}
