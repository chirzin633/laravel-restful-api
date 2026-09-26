<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    // basic auth
    Route::middleware('basic_auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('book')->group(function () {
            Route::post('/create', [BookController::class, 'create']);
        });
    });
});
