<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoController;

Route::middleware('web')->group(function(){
    Route::post('sso-logout', [SsoController::class, 'logout'])->name('sso.logout');
});