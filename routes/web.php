<?php

use App\Http\Controllers\Admin\AllowedIpController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'loginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::post('/', CurrencyController::class)->name('currency.fetch');

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::group([
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => 'can:admin'
    ], function () {
        Route::group([
            'prefix' => 'allowed-ips',
            'as' => 'allowed-ips.',
        ], function () {
            Route::get('/', [AllowedIpController::class, 'index'])->name('index');
            Route::post('/', [AllowedIpController::class, 'store'])->name('store');
            Route::delete('/{AllowedIp:id}', [AllowedIpController::class, 'destroy'])->name('destroy');
        });
    });
});
