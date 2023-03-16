<?php

namespace Core\View;

class View
{
    /**
     * Render a view file with optional parameters.
     *
     * @param  string  $view    The name of the view file to be rendered.
     * @param  array   $params  An associative array of data to be passed to the view.
     * @return void
     */
    public static function make($view, $params = [])
    {
        // Determine the path of the view file based on its name
        if (str_contains($view, '.')) {
            $file = view_path(str_replace('.', '/', $view) . ".php");
        } else {
            $file = view_path($view) . ".php";
        }

        // Extract the parameter names and values into variables
        foreach ($params as $param => $value) {
            $$param = $value;
        }

        // If the view file does not exist, show an error page
        if (!file_exists($file)) {
            View::error([
                'code' => '404',
                'title' => 'Page not found',
                'description' => "The view '$file' was not found on this server."
            ]);
        } else {
            // Otherwise, render the view file and output the result
            ob_start();
            include $file;
            ob_get_flush();
        }
    }

    /**
     * Show an error page with the given error information.
     *
     * @param  array  $error  An associative array containing error information.
     * @return void
     */
    public static function error(array $error)
    {
        // Get the error code, title, and description from the input array
        $code = $error['code'] ?? '500';
        $title = $error['title'] ?? 'Server Error';
        $description = $error['description'] ?? 'An error occurred on the server.';

        // Set the HTTP response code and render the error page
        http_response_code($code);
        View::make('errors.index', compact('code', 'title', 'description'));
    }
}
