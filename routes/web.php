<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    $html = file_get_contents(public_path('docs/index.html'));
    $html = str_replace('../docs/', './', $html);
    $html = str_replace('..\/docs\/', './', $html);
    return response($html)->header('Content-Type', 'text/html');
});

Route::get('/docs.openapi', function () {
    $path = public_path('vendor/scribe/openapi.yaml');

    return response()->file($path);
});

Route::get('/docs.postman', function () {
    $path = public_path('vendor/scribe/collection.json');

    return response()->json(json_decode(file_get_contents($path)));
});
