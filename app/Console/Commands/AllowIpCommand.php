<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AllowedIp;
use App\Rules\IpOrSubnet;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:allow-ip {range : An IP address or CIDR subnet, e.g. 192.168.1.0/24} {--description=}')]
#[Description('Add an IP address or subnet to the access whitelist')]
class AllowIpCommand extends Command
{
    public function handle(): int
    {
        $range = trim($this->argument('range'));

        if (!IpOrSubnet::isValid($range)) {
            $this->components->error("{$range} is not a valid IP address or CIDR subnet.");

            return self::FAILURE;
        }

        $allowedIp = AllowedIp::query()->firstOrCreate(
            ['range' => $range],
            ['description' => $this->option('description')],
        );

        $this->components->info($allowedIp->wasRecentlyCreated
            ? "{$range} added to the whitelist."
            : "{$range} is already whitelisted.");

        return self::SUCCESS;
    }
}
