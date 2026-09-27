<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class FetchCurrencyExchangeRates implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $this->startFetching();
        $validOn = Carbon::today()->toImmutable();

        // One job per base currency, so a single slow feed can't make the whole run time out.
        $jobs = Currency::query()
            ->get()
            ->map(fn (Currency $currency) => new ProcessCurrencyFeed($currency, $validOn));

        Bus::batch($jobs)
            ->name('Fetch currency exchange rates')
            ->allowFailures()
            ->then(static fn () => Cache::put(CurrencyService::CACHE_KEY_FETCHED_AT, now()->toAtomString()))
            ->finally(static fn () => Cache::forget(CurrencyService::CACHE_KEY_FETCHING))
            ->dispatch();
    }

    private function startFetching(): void
    {
        Cache::put(CurrencyService::CACHE_KEY_FETCHING, true, now()->addHour());
    }
}
