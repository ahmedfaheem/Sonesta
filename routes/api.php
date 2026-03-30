<?php

use App\Http\Controllers\Api\RoomsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/rooms', [RoomsController::class, 'index']);
});
