<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    // basic auth
    Route::middleware('basic_auth')->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    // sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('book')->group(function () {
            Route::post('/create', [BookController::class, 'create']);
            Route::put('/update/{id}', [BookController::class, 'update']);
            Route::get('/list', [BookController::class, 'list']);
            Route::delete('/delete/{id}', [BookController::class, 'delete']);
        });
    });
});
