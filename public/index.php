<?php

use Core\Http\Request;
use Core\Http\Route;

require_once __DIR__ . "/../src/Support/helpers.php";
require_once base_path("vendor/autoload.php");
require_once base_path("routes/web.php");



$request = new Request;
$route = new Route($request);
$route->resolve();

