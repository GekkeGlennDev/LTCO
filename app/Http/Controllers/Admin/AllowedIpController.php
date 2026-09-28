<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAllowedIpRequest;
use App\Models\AllowedIp;
use App\Services\IpWhitelistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AllowedIpController extends Controller
{
    public function index(Request $request): View
    {
        $allowedIps = AllowedIp::query()
            ->orderBy('range')
            ->get();

        return view('admin.allowed-ips.index', [
            'allowedIps' => $allowedIps,
            'currentIp' => $request->ip(),
        ]);
    }

    public function store(StoreAllowedIpRequest $request): RedirectResponse
    {
        $allowedIp = AllowedIp::query()
            ->create($request->validated());

        return redirect()
            ->route('admin.allowed-ips.index')
            ->with('status', "{$allowedIp->range} added to the whitelist.");
    }

    public function destroy(Request $request, AllowedIp $allowedIp, IpWhitelistService $whitelist): RedirectResponse
    {
        if (!$whitelist->allowsWithout($request->ip(), $allowedIp)) {
            return back()->withErrors([
                'allowed_ip' => "Removing {$allowedIp->range} would lock you out: it is the only entry allowing your IP ({$request->ip()}).",
            ]);
        }

        $allowedIp->delete();

        return redirect()
            ->route('admin.allowed-ips.index')
            ->with('status', "{$allowedIp->range} removed from the whitelist.");
    }
}
