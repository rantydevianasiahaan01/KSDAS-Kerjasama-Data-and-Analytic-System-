<?php

use App\Http\Controllers\Api\ApiDelcomController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'sso'], function () {
    // Route::post('/token', [App\Http\Controllers\SsoController::class, 'token']);
});

// Route::get('/test/crypto', [ApiDelcomController::class, 'testCryptoHelper']);

Route::get('/aplikasi/{judul}', [ApiDelcomController::class, 'getAplikasiByJudul']);

Route::group(['middleware' => 'api.check.auth'], function () {
    Route::post('/check-url', [ApiDelcomController::class, 'postCheckUrl']);

    Route::group(['prefix' => 'kursus'], function () {
        Route::get('/me/registered', [ApiDelcomController::class, 'getKursusMeRegistered']);
        Route::get('/{kursusId}/konten/delcom-grading', [ApiDelcomController::class, 'getKursusKontenDelcomGrading']);
        
        // CODE GRADING 
        Route::get('/-/konten/{kontenId}/code-grading', [ApiDelcomController::class, 'getKursusKontenDetailCodeGrading']);
        Route::post('/-/konten/{kontenId}/code-grading/test-cases', [ApiDelcomController::class, 'postKursusKontenDetailCodeGradingTestCases']);
        Route::post('/-/konten/{kontenId}/code-grading/submit', [ApiDelcomController::class, 'postKursusKontenDetailCodeGradingSubmit']);
        
        // API GRADING
        Route::get('/-/konten/{kontenId}/api-grading', [ApiDelcomController::class, 'getKursusKontenDetailApiGrading']);
        Route::post('/-/konten/{kontenId}/api-grading/submit', [ApiDelcomController::class, 'postKursusKontenDetailApiGradingSubmit']);

    });
});
