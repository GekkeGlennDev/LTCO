<?php

declare(strict_types=1);

namespace App\Processors;

use App\Models\Currency;
use App\Services\CurrencyService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

readonly class CurrencyFeedProcessor
{
    public function __construct(
        private CurrencyService $currencyService
    ) {
    }

    public function process(Currency $baseCurrency, CarbonImmutable $validOnDate): void
    {
        try {
            $feed = $this->currencyService->fetchFeed($baseCurrency);
        } catch (ConnectionException|RequestException $e) {
            logger()->error('Error fetching currency feed ' . $e->getMessage(), [
                'currency' => $baseCurrency->name,
            ]);

            return;
        }

        $this->currencyService->storeExchangeRates($baseCurrency, $feed, $validOnDate);
    }
}
