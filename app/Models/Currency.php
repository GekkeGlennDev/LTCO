<?php

namespace App\Models;

use App\Services\CurrencyService;
use Database\Factories\CurrencyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[UseFactory(CurrencyFactory::class)]
#[Fillable('name')]
class Currency extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        $flush = fn () => app(CurrencyService::class)->flushCache();

        static::saved($flush);
        static::deleted($flush);
    }

    public function exchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'base_currency_id');
    }

    public function latestExchangeRates(): Collection
    {
        return $this->exchangeRates()
            ->with(['baseCurrency', 'targetCurrency'])
            ->whereDate('valid_on', $this->exchangeRates()->max('valid_on'))
            ->get()
            ->toBase();
    }
}
