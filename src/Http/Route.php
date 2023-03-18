<?php

namespace Core\Http;

use Exception;

class Route
{
    protected static array $routes = [];
    protected Request $request;
    protected Response $response;

    /**
     * Constructor that takes a Request object
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Register a GET route
     */
    public static function get(string $path, callable|array|string $action): void
    {
        Route::setRoute(__FUNCTION__, $path, $action);
    }

    /**
     * Register a POST route
     */
    public static function post(string $path, callable|array|string $action): void
    {
        Route::setRoute(__FUNCTION__, $path, $action);
    }

    /**
     * Register a PUT route
     */
    public static function put(string $path, callable|array|string $action): void
    {
        Route::setRoute(__FUNCTION__, $path, $action);
    }

    /**
     * Register a DELETE route
     */
    public static function delete(string $path, callable|array|string $action): void
    {
        Route::setRoute(__FUNCTION__, $path, $action);
    }

    /**
     * Resolve the route and execute the corresponding action
     */
    public function resolve(): void
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();
        $contentType = strpos($path, '/api') === 0 ? 'application/json' : 'text/html';

        // Check if the requested method exists
        if (!isset(Self::$routes[$method])) {
            $data = [
                'code' => 404,
                'title' => 'Method Not Found',
                'description' => "The requested method '$method' was not found on this server.",
            ];
            $this->handleError($contentType, $data);
        }

        // Retrieve the callback for the specified path and method
        $action = Self::$routes[$method][$path] ?? false;


        // If the route doesn't exist, show a 404 error page
        if (!$action) {
            $data = [
                'code' => 404,
                'title' => 'Page Not Found',
                'description' => "The requested URL '$path' was not found on this server.",
            ];
            $this->handleError($contentType, $data);
        }

        // If the route maps to a callable function, call it with no parameters
        if (is_callable($action)) {
            $this->response->setBody(call_user_func_array($action, []))->setStatusCode(200)->setHeaders('Content-type', "$contentType")->send();
        }

        // If the route maps to a controller class name or a controller class method, try to execute it
        elseif (is_array($action) || is_string($action)) {
            try {
                // Parse the controller class name and method name from the route
                list($controllerName, $methodName) = is_array($action) ? $action : explode('@', $action);

                // Check if the specified controller exists
                if (!class_exists($controllerName)) {
                    $title = 'Controller Not Found';
                    $description = "The specified controller '$controllerName' does not exist.";
                    throw new Exception($description);
                }
                // Instantiate the specified controller
                $controller = new $controllerName;
                // Check if the specified method exists in the controller
                if (!method_exists($controller, $methodName)) {
                    $title = 'Method Not Found';
                    $description = "The specified method '$methodName' does not exist in '$controllerName' controller.";
                    throw new Exception($description);
                }
                // Execute the specified method in the controller with no parameters
                $this->response->setBody(call_user_func_array([$controller, $methodName], []))->setStatusCode(200)->setHeaders('Content-type', "$contentType")->send();
            } catch (Exception $e) {
                // If any errors occur, show the error page
                $data  = [
                    'code' => 404,
                    'title' => $title ?? 'Page Not Found',
                    'description' => $description ?? "The requested URL '$path' was not found on this server.",
                ];
                $this->handleError($contentType, $data);
            }
        }
    }


    private static function setRoute($method, $path, $action)
    {
        $debugTrace = debug_backtrace()[1]['file'];
        switch ($debugTrace) {
            case 'C:\Users\ghait\Desktop\MVC\routes\api.php':
                self::$routes[$method]["/api$path"] = $action;
                break;
            case 'C:\Users\ghait\Desktop\MVC\routes\web.php':
                self::$routes[$method][$path] =  $action;
                break;
        }
    }

    private function handleError($contentType, $data = []): void
    {
        if ($contentType == "application/json")
            $this->response->setStatusCode($data['code'])->json($data)->send();
        else
            $this->response->setStatusCode($data['code'])->viewError($data)->send();
    }
}
