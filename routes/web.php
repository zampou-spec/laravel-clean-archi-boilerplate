<?php

use Illuminate\Support\Facades\Route;
use Vimeo\Laravel\Facades\Vimeo;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return Vimeo::request('/me/videos/858109962', [], 'GET');
});
