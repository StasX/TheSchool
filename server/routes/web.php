<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/uploads/{filename}', function (string $filename) {
    $disk = Storage::disk('uploads');

    abort_unless($disk->exists($filename), 404);

    return response()->file($disk->path($filename));
})->withoutMiddleware([\Illuminate\Auth\Middleware\Authenticate::class]);
