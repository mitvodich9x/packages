<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Vgplay\Games\Http\Controllers\GameController;
use Vgplay\Games\Http\Middleware\ShareRolesMiddleware;
use Illuminate\Routing\Middleware;

Route::middleware(['web'])->group(function () {
    Route::get('/{alias}', [GameController::class, 'show']);
    Route::post('/roles', [GameController::class, 'get_role']);
    Route::post('/roles-by-server', [GameController::class, 'get_characters_by_server']);
    Route::post('/select-role', [GameController::class, 'selectRole'])->name('select-role');
});
