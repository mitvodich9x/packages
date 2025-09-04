<?php

use Illuminate\Support\Facades\Route;
use Vgplay\Recharge\Http\Controllers\ItemController;
use Vgplay\Recharge\Http\Controllers\ItemBrowseController;
use Vgplay\Recharge\Http\Controllers\ItemPaymentController;

Route::get('/games/{game}/items', [ItemController::class, 'index']);
Route::get('/games/{game}/units', [ItemController::class, 'unit']);
// Route::get('/games/{game}/items/{item}/methods', [ItemPaymentController::class, 'index']);
Route::get('/games/{game}/items/{item}/methods', [ItemPaymentController::class, 'methods']);
// Route::middleware('auth:sanctum')->group(function () {});
