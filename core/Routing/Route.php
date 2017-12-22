<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Http\RedirectResponse;
use Core\Routing\RouteMatcher;
use Core\Routing\ControllerDispatcher;
use Core\Exceptions\Http\MethodNotAllowedException;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

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

    /**
     * Route middleware
     *
     * @var array
     */
    private $middlewares;

    public function __construct(string $method, string $uri, $action, array $middlewares = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $this->parseAction($action);
        $this->middlewares = $middlewares;
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
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app ApplicationInterface
     * @param Core\Http\Request $request Request to bind route
     * @return void
     */
    public function bind(ApplicationInterface $app, Request $request)
    {
        $this->matchRequestMethod($request);

        $response = $this->runMiddlewares($app, $request);

        return $this->resolveMiddlewareResponse($app, $response);
    }

    /**
     * Run global middlewares from configuration
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app ApplicationInterface
     * @param Core\Http\Request $request Request to run middlewares
     * @return void
     */
    private function runMiddlewares(ApplicationInterface $app, Request $request)
    {
        return Middleware::boot($app, $request)->run($this->middlewares);
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
     * Resolve response from middleware
     * Response can be a request to be dispached or can be a redirect response
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app ApplicationInterface
     * @param Core\Http\Request|Core\Http\RedirectResponse $response Response to resolve
     * @return mixed
     */
    private function resolveMiddlewareResponse(ApplicationInterface $app, $response)
    {
        // Redirect to route
        if ( $response instanceof RedirectResponse ) {
            return $response;
        }

        // Run anonymous function
        if ( $this->isCallable() ) {
            return $this->runCallable($response);
        }

        // Run controller action
        return (new ControllerDispatcher(
            $app, $response, $this->getController(), $this->getAction(), $this->middlewares)
        )->dispatch();
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
