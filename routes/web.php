<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:req-limit'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('app.beranda');
    })->name('/');

    Route::group(['prefix' => 'sso'], function () {
        Route::get('/callback', [AuthController::class, 'ssoCallback'])->name('sso.callback');
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::get('/login/redirect/{redirectToken}', [AuthController::class, 'loginRedirect'])->name('auth.login.redirect');
        Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::get('/login/qrcode', [AuthController::class, 'loginQRCode'])->name('auth.login.qrcode');
        Route::get('/totp', [AuthController::class, 'totp'])->name('auth.totp');
        Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    Route::group(['prefix' => 'legal'], function () {
        Route::get('/privacy', [AppController::class, 'privacy'])->name('legal.privacy');
        Route::get('/terms', [AppController::class, 'terms'])->name('legal.terms');
    });

    Route::group(['middleware' => 'check.auth'], function () {
        Route::group(['prefix' => 'app'], function () {
            Route::get('/beranda', [AppController::class, 'umumBeranda'])->name('app.beranda');
            Route::get('/hak-akses', [AppController::class, 'hakAkses'])->name('app.hak-akses');
            Route::get('/profile', [AppController::class, 'profile'])->name('app.profile');
        });

        Route::group(['prefix' => 'gate'], function () {
            Route::get('/tutorial/{id}', [GateController::class, 'tutorial'])->name('gate.tutorial');
        });
    });
});
