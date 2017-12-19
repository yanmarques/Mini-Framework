<?php

namespace Core\Routing;

use Core\Routing\Route;
use Core\Routing\Validators\UriValidator;
use Core\Routing\Validators\ActionValidator;
use Core\Routing\Validators\NoValidator;

class RouteManager
{
    /**
     * @var array
     */
    private $routes;

    /**
     * Constructor of class
     *
     * @return Core\Routing\RouteManager
     */
    public function __construct()
    {
        $this->routes = stack();
    }

    /**
     * Parse route manager and add to routes list
     *
     * @param Core\Routing\Manager $manager Route manager with prefix and action
     * @return void
     */
    public function addRoute(Route $route)
    {
        $this->validate($route);
        $this->routes->add($route);
    }

    /**
     * Validate route using the chain of responsability design pattern
     * to pass for each validator and try to validate the route
     * In case any route is not valid an Exception will be throw
     *
     * @throws Exception
     *
     * @param Core\Routing\Route $route Route to validate
     * @return void
     */
    public function validate(Route $route)
    {
        $validators = (stack($this->getValidators()))
            ->mapWithNext(function ($value, $next) {

                // Has next element
                if ( $next ) {
                    $value->setNext($next);
                }

                return $value;
            }
        );

        $validators->first()->matches($route);
    }

    /**
     * Get all route validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return [
            new UriValidator,
            new ActionValidator,
            new NoValidator
        ];
    }

    /**
     * Get all registered routes
     * 
     * @return Core\Stack\Stack
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Merge routes
     * 
     * @param array $routes Routes to merge
     * @return Core\Routing\RouteManager
     */
    public function merge(array $routes)
    {
        $this->routes = $this->routes->merge($routes);
        return $this;
    }
}
