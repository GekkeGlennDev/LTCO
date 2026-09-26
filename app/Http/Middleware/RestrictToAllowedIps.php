<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\IpWhitelistService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class RestrictToAllowedIps
{
    public function __construct(private IpWhitelistService $whitelist) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->whitelist->allows($request->ip())) {
            abort(Response::HTTP_FORBIDDEN, "Access from your IP address ({$request->ip()}) is not allowed.");
        }

        return $next($request);
    }
}
