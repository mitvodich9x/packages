<?php

use Illuminate\Support\Facades\Route;
use Vgplay\Recharge\Http\Controllers\ItemController;
use Vgplay\Recharge\Http\Controllers\OrderController;
use Vgplay\Recharge\Http\Controllers\PaymentController;
use Vgplay\Recharge\Http\Controllers\PurchaseController;
use Vgplay\Recharge\Http\Controllers\ItemBrowseController;
use Vgplay\Recharge\Http\Controllers\ItemPaymentController;

// Route::get('/games/{game}/items', [ItemController::class, 'index']);
// Route::get('/games/{game}/units', [ItemController::class, 'unit']);
// // Route::get('/games/{game}/items/{item}/methods', [ItemPaymentController::class, 'index']);
// Route::get('/games/{game}/items/{item}/methods', [ItemPaymentController::class, 'methods']);
// // Route::middleware('auth:sanctum')->group(function () {});

Route::get('/games/{game}/items', [ItemController::class, 'index'])->name('games.items.index');

// API:
Route::get('/games/{game}/items/{item}', [ItemController::class, 'show'])->name('games.items.show');
Route::get('/games/{game}/items/{item}/methods', [PaymentController::class, 'methods'])->name('games.items.methods');

// Lịch sử mua cho UI kiểm tra limit / tier
Route::get('/games/{game}/users/{vgpId}/purchases', [PurchaseController::class, 'history'])->name('games.users.purchases');

// Tạo order (giả lập bước tạo bill)
Route::post('/games/{game}/items/{item}/orders', [OrderController::class, 'store'])->name('games.items.orders.store');
