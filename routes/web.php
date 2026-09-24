<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Semua route dikelola oleh masing-masing Service Provider modul.
| Tambahkan modul baru di bootstrap/providers.php
|--------------------------------------------------------------------------
|
| Website routes   → app/Modules/Website/Routes/web.php
| Shortlink routes → app/Modules/Shortlink/Routes/web.php
|
*/

// Fallback route untuk Service Worker & PWA Manifest
Route::get('/sw.js', function () {
    $path = public_path('sw.js');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Service-Worker-Allowed' => '/',
        ]);
    }

    return response('// Service Worker placeholder', 200, [
        'Content-Type' => 'application/javascript; charset=UTF-8',
        'Service-Worker-Allowed' => '/',
    ]);
});

Route::get('/manifest.json', function () {
    $path = public_path('manifest.json');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    return response('{}', 200, [
        'Content-Type' => 'application/json; charset=UTF-8',
    ]);
});
