<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\TransactionApiController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/auth/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/transactions', [TransactionApiController::class, 'index']);
});
