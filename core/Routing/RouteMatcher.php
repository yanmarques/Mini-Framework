<?php

namespace Core\Routing;

class RouteMatcher
{
    /**
     * Route to match.
     *
     * @var Core\Routing\Route
     */
    private $route;

    /**
     * Constructor of class.
     *
     * @param Core\Routing\Route $route
     *
     * @return Core\Routing\RouteMatcher
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Check an route prefix against an uri.
     *
     * @param string $uri Request uri
     *
     * @return bool
     */
    public function matchAgainstRoute(string $uri, string $method)
    {
        // Check request URI against route URI
        $matches = preg_match($this->buildPattern(), $uri, $uriFound);

        // If regex got something
        if ($matches && !empty($uriFound)) {

            // Check if match found is like the request URI
            return $uriFound[0] == $uri;
        }

        return false;
    }

    /**
     * Build the regex pattern for route prefix.
     *
     * @return string
     */
    private function buildPattern()
    {
        $url = str_replace('/', '\/', $this->route->uri());

        return "/\/?($url)+\/?/";
    }
}
