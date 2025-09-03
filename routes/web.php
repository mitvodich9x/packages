<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/media/{path}', function (string $path) {
    $path = urldecode($path);
    abort_unless(Storage::disk('ftp')->exists($path), 404);
    return Storage::disk('ftp')->response($path); // stream từ FTP
})->where('path', '.*')->name('media.show');
