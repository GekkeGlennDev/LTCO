<?php

use App\Console\Commands\FetchCurrencyFeed;

Schedule::call(FetchCurrencyFeed::class)->daily();
