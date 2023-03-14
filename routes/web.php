<?php

use App\Controllers\HomeController;
use Core\Http\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/string', "App\Controllers\HomeController@index");
Route::get('/user', function () {
    echo "this is" .  $_SERVER['REQUEST_URI'];
});
