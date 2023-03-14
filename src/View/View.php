<?php

namespace Core\View;

class View
{
    public static function make($view, $params = [])
    {
        if (str_contains($view, '.')) {
            $file = view_path(str_replace('.', '/', $view) . ".php");
        } else {
            $file = view_path($view) . ".php";
        }
        foreach ($params as $param => $value) {
            $$param = $value;
        }
        if (!file_exists($file)) {
            View::make('errors.index', [
                'code' => '404',
                'title' => 'Page not found',
                'description' => 'The view you are trying to reach does not exist. Please check your view name or file path.'
            ]);
        } else {
            ob_start();
            include $file;
            ob_get_flush();
        }
    }
}
