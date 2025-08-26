<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('manifest.json', function () {
    $manifest = config('laravel-pwa-kit.manifest', []);
    return Response::json($manifest, 200, [
        'Content-Type' => 'application/manifest+json',
    ]);
})->name('laravel-pwa-kit.manifest-json');