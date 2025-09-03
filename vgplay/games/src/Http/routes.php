<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Vgplay\Games\Http\Controllers\GameController;
use Vgplay\Games\Http\Middleware\ShareRolesMiddleware;
use Illuminate\Routing\Middleware;

Route::middleware(['web'])->group(function () {
    Route::inertia('/', 'Home');
});
