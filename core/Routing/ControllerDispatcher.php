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
        $this->reflector = new Reflector($app->fileHandler());
    }

    /**
     * Dispatch the controller action with request parameter
     *
     * @return mixed
     */
    public function dispatch()
    {
        return $this->reflector->bind($this->controller)
            ->callMethod($this->action, [$this->request]);
    }
}
