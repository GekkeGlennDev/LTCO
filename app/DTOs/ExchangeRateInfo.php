<?php

declare(strict_types=1);

namespace App\DTOs;

use App\ValueObjects\Name;
use App\ValueObjects\Rate;

readonly class ExchangeRateInfo
{
    public function __construct(
        public Name $currency,
        public Rate $rate,
    ) {
    }
}
