<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Routing\RouteMatcher;
use Core\Routing\ControllerDispatcher;
use Core\Exceptions\Http\MethodNotAllowedException;

class Route
{
    /**
     * Route HTTP method
     *
     * @var string
     */
    private $method;

    /**
     * Route URI
     *
     * @var string
     */
    private $uri;

    /**
     * Route action
     *
     * @var Closure|string
     */
    private $action;

    /**
     * Route matcher handles matching a route with given URI
     *
     * @var Core\Routing\RouteMatcher
     */
    private $matcher;

    /**
     * Request bound to route
     *
     * @var Core\Http\Request
     */
    private $request;

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $this->parseAction($action);
    }

    /**
     * Get URI route
     *
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Get route method
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Get the full route action
     *
     * @return string
     */
    public function action()
    {
        if ( $this->isCallable() ) {
            return $this->action;
        }  

        return $this->getController() .'@'. $this->getAction();
    }

    /**
     * Get the route action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action['action'];
    }

    /**
     * Get the route controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->action['controller'];
    }

    /**
     * Check wheter route action is an anonymous function
     *
     * @return bool
     */
    public function isCallable()
    {
        return is_callable($this->action);
    }

    /**
     * Match the route URI against URI request
     *
     * @param Core\Http\Request $request
     * @return bool
     */
    public function matches(Request $request)
    {
        return $this->getMatcher()->matchAgainstRoute($request->uri(), $request->method());
    }

    /**
     * Bind request to route
     *
     * @param Core\Http\Request $request Request to bind route
     * @return void
     */
    public function bind(Request $request)
    {
        $this->matchRequestMethod($request);

        // Run anonymous function
        if ( $this->isCallable() ) {
            return $this->runCallable($request);
        }

        // Run controller action
        return (new ControllerDispatcher(
            $request, $this->getController(), $this->getAction())
        )->dispatch();
    }

    /**
     * Match the route method against the request method
     *
     * @throws Core\Exceptions\Http\MethodNotAllowedException
     *
     * @param Core\Http\Request $request Request to match route method
     * @return Core\Routing\RouteMatcher
     */
    private function matchRequestMethod(Request $request)
    {
        if ( $this->method != $request->method() ) {
            throw new MethodNotAllowedException("Method not allowed");
        }

        return $this;
    }

    /**
     * Get a route matcher
     *
     * @return Core\Routing\RouteMatcher
     */
    private function getMatcher()
    {
        return $this->matcher ?: new RouteMatcher($this);
    }

    /**
     * Run controller action
     *
     * @param Core\Http\Request $request
     * @return mixed
     */
    private function runCallable(Request $request)
    {
        return call_user_func($this->action, $request);
    }

    /**
     * Parse the action checking wheter is a function or a controller
     *
     * @param mixed $action
     * @return mixed
     */
    private function parseAction($action)
    {
        if ( is_callable($action) ) {
            return $action;
        }

        $action = explode('@', $action);
        return ['controller' => $action[0], 'action' => $action[1]];
    }
}
