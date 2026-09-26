<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'loginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::view('/', 'home')->name('home');

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
