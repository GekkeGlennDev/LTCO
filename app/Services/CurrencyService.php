<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\ValueObjects\Name;
use App\ValueObjects\Rate;
use Carbon\CarbonImmutable;

class CurrencyService
{
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
