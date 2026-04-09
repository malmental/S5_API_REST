<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return view('scribe.index');
});

Route::get('/docs.openapi', function () {
    $path = public_path('vendor/scribe/openapi.yaml');

    return response()->file($path);
});

Route::get('/docs.postman', function () {
    $path = public_path('vendor/scribe/collection.json');

    return response()->json(json_decode(file_get_contents($path)));
});

Route::get('/docs', function () {
    return view('scribe.index');
});

Route::get('/docs.openapi', function () {
    $path = public_path('vendor/scribe/openapi.yaml');

    return response()->file($path);
});

Route::get('/docs.postman', function () {
    $path = public_path('vendor/scribe/collection.json');

    return response()->json(json_decode(file_get_contents($path)));
});

Route::get('/docs', function () {
    return view('scribe.index');
});

Route::get('/docs.openapi', function () {
    $path = public_path('vendor/scribe/openapi.yaml');

    return response()->file($path);
});

Route::get('/docs.postman', function () {
    $path = public_path('vendor/scribe/collection.json');

    return response()->json(json_decode(file_get_contents($path)));
});
