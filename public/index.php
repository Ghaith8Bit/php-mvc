<?php

use Core\Http\Request;
use Core\Http\Route;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../routes/web.php";

$request = new Request;
$route = new Route($request);
$route->resolve();