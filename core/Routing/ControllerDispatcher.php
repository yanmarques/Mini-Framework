<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Reflector\Reflector;

class ControllerDispatcher
{
    /**
     * Controller action.
     *
     * @var string
     */
    private $action;

    /**
     * Controller name.
     *
     * @var string
     */
    private $controller;

    /**
     * Path to controllers.
     *
     * @var string
     */
    private $controllersPath = 'App\\Http\\Controllers\\';

    /**
     * Request to pass as parameter on controller action.
     *
     * @var Core\Http\Request
     */
    private $request;

    public function __construct(Request $request, string $controller, string $action)
    {
        $this->request = $request;
        $this->controller = $this->controllersPath.$controller;
        $this->action = $action;
    }

    /**
     * Dispatch the controller action with request parameter.
     *
     * @return mixed
     */
    public function dispatch()
    {
        return Reflector::bind($this->controller)
            ->callMethod($this->action, [$this->request]);
    }
}
