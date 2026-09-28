<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\FetchCurrencyExchangeRates;
use App\Services\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Bus;

class CurrencyController
{
    public function __invoke(CurrencyService $currencyService): RedirectResponse
    {
        if (!$currencyService->isFetching()) {
            Bus::dispatch(new FetchCurrencyExchangeRates());
        }

        return redirect()
            ->route('home')
            ->with('status', 'Starting fetching currency exchange rates. This may take a while.');
    }
}
