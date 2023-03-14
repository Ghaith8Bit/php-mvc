<?php

use Core\View\View;

if (!function_exists('env')) {
    function env($key, $value = null)
    {
        return $_ENV[$key] ?? value($value);
    }
}

if (!function_exists('value')) {
    function value($value)
    {
        return ($value instanceof Closure) ? $value() : $value;
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        return dirname(__DIR__, 2) . "/$path";
    }
}

if (!function_exists('view_path')) {
    function view_path($path = '')
    {
        return dirname(__DIR__, 2) . "/views/$path";
    }
}

if (!function_exists('view')) {
    function view($view, $params = [])
    {
        View::make($view, $params);
    }
}
