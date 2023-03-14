<?php

namespace Core\Http;

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

    public function resolve()
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();

        $action = Self::$routes[$method][$path] ?? false;

        if (!$action) {
            //Show the 404.php
        } else {
            if (is_callable($action)) {
                call_user_func_array($action, []);
            } elseif (is_array($action)) {
                try {
                    $controllerName = $action[0];
                    $methodName = $action[1];
                    if (!class_exists($controllerName))
                        throw new Exception("There's no such controller");
                    if (!method_exists($controllerName, $methodName))
                        throw new Exception("There's no such method in $controllerName controller");
                    call_user_func_array([new $controllerName, $methodName], []);
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            } elseif (is_string($action)) {
                try {
                    list($controllerName, $methodName) = explode('@', $action);
                    if (!class_exists($controllerName))
                        throw new Exception("There's no such controller");
                    if (!method_exists($controllerName, $methodName))
                        throw new Exception("There's no such method in $controllerName controller");
                    call_user_func_array([new $controllerName, $methodName], []);
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
    }
}
