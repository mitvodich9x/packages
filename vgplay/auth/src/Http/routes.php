<?php

use Illuminate\Support\Facades\Route;
use Vgplay\Auth\Http\Controllers\AuthController;
use Vgplay\Auth\Http\Controllers\FacebookController;

Route::middleware('web')->group(function () {
    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/oauth/facebook/login', [FacebookController::class, 'login']);
    Route::get('/oauth/facebook/callback/{game}', [FacebookController::class, 'callback']);
    Route::get('/balance', [AuthController::class, 'getBalance'])->name('balance');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
});
