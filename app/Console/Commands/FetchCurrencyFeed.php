<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

#[Signature('app:fetch-currency-feed')]
#[Description('Fetch currency feeds')]
class FetchCurrencyFeed extends Command
{
    const string URL = 'https://www.floatrates.com/daily/%s.json';

    public function handle(): void
    {
        $date = Carbon::today()->format('Y-m-d');
        $disk = Storage::disk('currency_feed');

        $feeds = $this->supportedFeeds();
        $this->output->progressStart(count($feeds));

        foreach ($feeds as $feed) {
            $fileName = sprintf('%s/%s.json', $date, $feed);
            $disk->put($fileName, file_get_contents(sprintf(self::URL, $feed)));
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    // todo, Improve.
    private function supportedFeeds(): array
    {
        return [
            'eur',
            'aud',
            'cad',
            'chf',
            'cny',
            'gbp',
            'hkd',
            'idr',
            'inr',
            'jpy',
            'krw',
            'myr',
            'nzd',
            'pgk',
            'php',
            'sgd',
            'thb',
            'twd',
            'usd',
            'vnd',
            'aed',
            'qar',
            'sar',
            'dkk',
            'egp',
            'ils',
            'jod',
            'nok',
            'sek',
            'zar',
            'brl',
            'clp',
            'czk',
            'huf',
            'isk',
            'mxn',
            'pln',
            'ron',
            'try',
            'uah',
            'mdl',
            'rsd',
            'rub',
            'azn',
            'bdt',
            'dzd',
            'gel',
            'kzt',
            'tnd',
            'xaf',
            'xof',
            'byn',
            'pkr',
            'afn',
            'all',
            'amd',
            'aoa',
            'ars',
            'awg',
            'bam',
            'bbd',
            'bhd',
            'bif',
            'bnd',
            'bob',
            'bsd',
            'bwp',
            'bzd',
            'cdf',
            'cop',
            'crc',
            'cup',
            'cve',
            'djf',
            'dop',
            'ern',
            'etb',
            'fjd',
            'ghs',
            'gip',
            'gmd',
            'gnf',
            'gtq',
            'gyd',
            'hnl',
            'htg',
            'iqd',
            'jmd',
            'kes',
            'kgs',
            'khr',
            'kmf',
            'kwd',
            'kyd',
            'lbp',
            'lkr',
            'lrd',
            'lsl',
            'lyd',
            'mad',
            'mga',
            'mkd',
            'mmk',
            'mnt',
            'mop',
            'mru',
            'mur',
            'mvr',
            'mwk',
            'mzn',
            'nad',
            'ngn',
            'nio',
            'npr',
            'omr',
            'pab',
            'pen',
            'pyg',
            'rwf',
            'sbd',
            'scr',
            'sdg',
            'sos',
            'srd',
            'ssp',
            'stn',
            'svc',
            'szl',
            'tjs',
            'tmt',
            'top',
            'ttd',
            'tzs',
            'ugx',
            'uyu',
            'uzs',
            'ves',
            'vuv',
            'wst',
            'xcd',
            'xcg',
            'xpf',
            'yer',
            'zmw',
            'irr',
            'lak',
            'syp',
        ];
    }
}
