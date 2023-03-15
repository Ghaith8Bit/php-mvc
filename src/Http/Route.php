<?php

namespace Core\Http;

use Core\View\View;
use Exception;

class Route
{
    protected static array $routes;
    protected $request;
    public function __construct(Request $req)
    {
        $this->request = $req;
    }

    //This function for creating get request routes 
    public static function get(string $path, callable|array|string $action)
    {
        Self::$routes['get'][$path] = $action;
    }

    //This function for creating post request routes 
    public static function post(string $path, callable|array|string $action)
    {
        Self::$routes['post'][$path] = $action;
    }

    //This function for creating put request routes 
    public static function put(string $path, callable|array|string $action)
    {
        Self::$routes['put'][$path] = $action;
    }

    //This function for creating delete request routes 
    public static function delete(string $path, callable|array|string $action)
    {
        Self::$routes['delete'][$path] = $action;
    }


    public function resolve()
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();

        $action = Self::$routes[$method][$path] ?? false;

        if (!$action) {
            View::make('errors.index', [
                'code' => '404',
                'title' => 'Page not found',
                'description' => 'The page you are looking for might have been removed had its name changed or is temporarily unavailable.'
            ]);
        } else {
            if (is_callable($action)) {
                call_user_func_array($action, []);
            } elseif (is_array($action)) {
                try {
                    // Extract the controller and method names from the route
                    $controllerName = $action[0];
                    $methodName = $action[1];

                    // Check if the specified controller exists
                    if (!class_exists($controllerName)) {
                        throw new Exception("Invalid Controller: '$controllerName'");
                    }

                    // Check if the specified method exists in the controller
                    if (!method_exists($controllerName, $methodName)) {
                        throw new Exception("Invalid Method: '$methodName' in '$controllerName' controller");
                    }

                    // Call the specified method in the controller
                    call_user_func_array([new $controllerName, $methodName], []);
                } catch (Exception $e) {
                    // If an error occurs, show the error page
                    View::make('errors.index', [
                        'code' => '404',
                        'title' => 'Page Not Found',
                        'description' => $e->getMessage()
                    ]);
                }
            } elseif (is_string($action)) {
                try {
                    // Extract the controller and method names from the route
                    list($controllerName, $methodName) = explode('@', $action);

                    // Check if the specified controller exists
                    if (!class_exists($controllerName)) {
                        throw new Exception("Invalid Controller: '$controllerName'");
                    }

                    // Check if the specified method exists in the controller
                    if (!method_exists($controllerName, $methodName)) {
                        throw new Exception("Invalid Method: '$methodName' in '$controllerName' controller");
                    }

                    // Call the specified method in the controller
                    call_user_func_array([new $controllerName, $methodName], []);
                } catch (Exception $e) {
                    // If an error occurs, show the error page
                    View::make('errors.index', [
                        'code' => '404',
                        'title' => 'Page Not Found',
                        'description' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
