<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Exceptions\Http\NotFoundException;
use Core\Bootstrapers\Application;

class RouteResolver
{
    /**
     * Router
     *
     * @var Core\Routing\Router
     */
    private $router;

    /**
     * Application
     *
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * Constructor of class
     *
     * @param Core\Routing\Router $router
     * @return Core\Routing\RouterResolver
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    /**
     * Dispatch an request to match a route
     *
     * @param Core\Http\Request $request
     */
    public function dispatch(Request $request)
    {
        $route = $this->router->getRoutes()->reject(function ($route) use ($request) {
            return ! $route->matches($request);
        })->first();

        // No matches found
        if ( ! $route ) {
            throw new NotFoundException('Not found');
        }

        return $route->bind($request);
    }
}
