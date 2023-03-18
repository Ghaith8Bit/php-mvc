<?php

use Core\Http\Route;

Route::get('/test', function () {
    echo "this is" .  $_SERVER['REQUEST_URI'];
});
