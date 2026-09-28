<?php

namespace App\Models;

use App\Services\IpWhitelistService;
use Database\Factories\AllowedIpFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $range
 * @property ?string $description
 * @property Carbon $created_at
 */
#[UseFactory(AllowedIpFactory::class)]
#[Fillable('range', 'description')]
class AllowedIp extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        $flush = fn () => app(IpWhitelistService::class)->flushCache();

        static::saved($flush);
        static::deleted($flush);
    }
}
