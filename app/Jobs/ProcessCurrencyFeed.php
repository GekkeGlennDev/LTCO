<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Processors\CurrencyFeedProcessor;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessCurrencyFeed implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public Currency $currency,
        public CarbonImmutable $validOn,
    ) {
    }

    public function handle(CurrencyFeedProcessor $processor): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $processor->process($this->currency, $this->validOn);
    }
}
