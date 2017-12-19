<?php

namespace Core\Routing;

use Core\Bootstrapers\Application;
use Core\Http\Request;
use Core\Reflector\Reflector;
use Core\Contracts\Http\MiddlewareInterface;

class ControllerDispatcher
{
    /**
     * Controller action
     *
     * @var string
     */
    private $action;

    /**
     * Controller name
     *
     * @var string
     */
    private $controller;

    /**
     * Application
     *
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * Reflector class to dinamically access classes
     *
     * @var Core\Reflector\Reflector
     */
    private $reflector;

    /**
     * Path to controllers
     *
     * @var string
     */
    private $controllersPath = 'App\\Http\\Controllers\\';

    /**
     * Request to pass as parameter on controller action
     *
     * @var Core\Http\Request
     */
    private $request;

    public function __construct(Application $app, Request $request, string $controller, string $action)
    {
        $this->request = $request;
        $this->controller = $this->controllersPath . $controller;
        $this->action = $action;
        $this->app = $app;
        $this->reflector = new Reflector($app->fileHandler());
    }

    /**
     * Dispatch the controller action with request parameter
     *
     * @return mixed
     */
    public function dispatch()
    {
        $this->runMiddlewares();

        return $this->reflector->bind($this->controller)
            ->callMethod($this->action, [$this->request]);
    }

    /**
     * Run global middlewares from configuration
     *
     * @return void
     */
    private function runMiddlewares()
    {
        $interface = "Core\\Interfaces\\Http\\MiddlewareInterface";
        $request = $this->request;

        $global = $this->app->middleware()->global;
        $global->each(function ($value) use (&$request, $interface) {
            $class = (new Reflector($this->app->fileHandler()))->bind($value);

            if ( ! $class->implementsInterface($interface) ) {
                throw new \RuntimeException("Middleware [$value] must implements [$interface]");
            }

            $request = $class->callMethod("apply", [$request]);
        });

        $this->request = $request;
    }
}
