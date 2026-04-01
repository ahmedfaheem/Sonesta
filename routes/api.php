<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.banned'])->group(function () {
    Route::post('/tokens', [AuthTokenController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/rooms', [RoomsController::class, 'index']);

    Route::prefix('analytics')->name('api.analytics.')->middleware('role:admin|manager|receptionist')->group(function () {
        Route::get('/revenue', [AnalyticsController::class, 'revenuePerMonth'])->name('revenue');
        Route::get('/reservations-by-country', [AnalyticsController::class, 'reservationsByCountry'])->name('reservations.by_country');
        Route::get('/gender-ratio', [AnalyticsController::class, 'genderRatio'])->name('gender_ratio');
        Route::get('/top-clients', [AnalyticsController::class, 'topClients'])->name('top_clients');
    });
});
