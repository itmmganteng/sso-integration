<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoController;

Route::middleware('web')->group(function(){
    Route::post('sso-logout', [SsoController::class, 'logout'])->name('sso.logout');
});

Route::middleware(['web', 'authenticated.sso'])->group(function () {
    Route::get('sso/auth', function () {
        return view('pages.sso.auth');
    })->name('sso.auth');

    Route::get('callback', [SsoController::class, 'callback'])->name('sso.callback');
});