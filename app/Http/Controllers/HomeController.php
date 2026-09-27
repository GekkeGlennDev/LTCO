<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ConvertCurrencyRequest;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ConvertCurrencyRequest $request, CurrencyService $currencyService): View
    {
        $currency = $request->string('currency', 'eur')->lower()->value();
        $amount = (float)$request->input('amount', 1);

        $currencyModel = Currency::query()->where('name', strtolower($currency))->first();
        $rates = $currencyModel->latestExchangeRates()
            ->sortBy(fn (ExchangeRate $rate) => $rate->targetCurrency->name)
            ->values();

        $isFetching = $currencyService->isFetching();
        $lastFetched = $currencyService->getLastFetched()?->toDateTimeString() ?? 'Never';

        return view('home', [
            'currencies' => $currencyService->all(),
            'currency' => $currency,
            'amount' => $amount,
            'rates' => $rates,
            'lastFetched' => $isFetching ? 'Fetching...' : $lastFetched,
            'fetching' => $isFetching,
        ]);
    }
}
