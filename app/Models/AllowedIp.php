<?php

namespace App\Models;

use App\Services\IpWhitelistService;
use Database\Factories\AllowedIpFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(AllowedIpFactory::class)]
#[Fillable('range', 'description')]
class AllowedIp extends Model
{
    use HasFactory;
}
