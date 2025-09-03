<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Vgplay\Recharges\Http\Controllers\RechargeController;
use Vgplay\Recharges\Http\Middleware\ShareRolesMiddleware;
use Illuminate\Routing\Middleware;

Route::middleware(['web'])->group(function () {
    Route::get('/{alias}', [RechargeController::class, 'show']);
    Route::post('/roles', [RechargeController::class, 'get_role']);
    Route::post('/roles-by-server', [RechargeController::class, 'get_characters_by_server']);
    Route::post('/select-role', [RechargeController::class, 'selectRole'])->name('select-role');
});
