<?php

use App\Http\Controllers\SsoController;

Route::post('sso-logout', [SsoController::class, 'logout'])->name('sso.logout');