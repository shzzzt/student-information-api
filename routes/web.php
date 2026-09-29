<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Convenience alias: the canonical Swagger UI route is /api/docs (see config/l5-swagger.php).
// The destination must be absolute, otherwise it is resolved relative to the request URI.
Route::redirect('api/documentation', '/api/docs');
