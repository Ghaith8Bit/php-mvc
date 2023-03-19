<?php

namespace Core;

use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Route;
use Dotenv\Dotenv;


require_once base_path("routes/web.php");
require_once base_path("routes/api.php");

class Application
{
    protected Request $request;
    protected Response $response;
    protected Route $route;
    protected Dotenv $env;

    public function __construct()
    {
        $this->request = new Request;
        $this->response = new Response;
        $this->route = new Route($this->request, $this->response);
        $this->env = Dotenv::createImmutable(base_path());
    }

    public function run()
    {
        $this->route->resolve();
        $this->env->load();
    }
}
