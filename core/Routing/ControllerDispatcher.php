<?php

namespace Core\Routing;

use \ReflectionClass;
use Core\Http\Request;

class ControllerDispatcher
{
    /**
     * Controller action
     *
     * @var string
     */
    private $action;

    /**
     * Controller
     *
     * @var string
     */
    private $controller;

    /**
     * @var Core\Application\Container
     */
    private $container;

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

    public function __construct(Request $request, string $controller, string $action)
    {
        $this->request = $request;
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * Dispatch the controller action with request parameter
     *
     * @return mixed
     */
    public function dispatch()
    {
        $reflector = (new ReflectionClass($this->getControllerPath()));

        if ( ! $reflector->hasMethod($this->action) ) {
            throw new \Exception("Method [$this->action] do not exists on [{$this->getControllerPath()}]");
        }

        return ($reflector->newInstance())->{$this->action}($this->request);
    }

    /**
     * Build the controller  file path
     *
     * @return string
     */
    private function getControllerPath()
    {
        return $this->controllersPath . $this->controller;
    }
}
