<?php

use App\Http\Controllers\Api\V1\AccessTokenController;
use App\Http\Controllers\Api\V1\CandidateController;
use App\Http\Controllers\Api\V1\RegisteredUserController;
use App\Http\Controllers\Api\V1\TeamRegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AccessTokenController::class, 'store'])->name('login');

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AccessTokenController::class, 'destroy'])->name('logout');

        Route::get('/candidate', [CandidateController::class, 'show'])->name('candidate.show');
        Route::post('/candidate/team', [TeamRegistrationController::class, 'store'])->name('candidate.team.store');
    });
});
