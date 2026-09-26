<?php

namespace App\Models;

use Database\Factories\ExchangeRateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseFactory(ExchangeRateFactory::class)]
#[Fillable('base_currency_id', 'target_currency_id', 'rate', 'valid_on')]
class ExchangeRate extends Model
{
    use HasFactory;

    protected $casts = [
        'rate' => 'float',
        'valid_on' => 'date',
    ];

    public function baseCurrency(): HasOne
    {
        return $this->hasOne(Currency::class, 'id', 'base_currency_id');
    }

    public function targetCurrency(): HasOne
    {
        return $this->hasOne(Currency::class, 'id', 'target_currency_id');
    }
}
