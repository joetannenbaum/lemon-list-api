<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return null;
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/terms-and-conditions', function () {
    return view('terms-and-conditions');
});

Route::get('/list/{uuid}', function () {
    return null;
});
