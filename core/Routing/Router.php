<?php

namespace Core\Routing;

use Core\Routing\RouteManager;
use Core\Routing\Route;

class Router extends RouteManager
{
    /**
     * Constructor of class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET Route
     * 
     * @param string $url The prefix of url
     * @param Closure|string
     */
    public function get(string $url, $action)
    {
        $this->add('GET', $url, $action);
    }

    /**
     * PUT Route
     * 
     * @param string $url The prefix of url
     * @param Closure|string
     */
    public function put(string $url, $action)
    {
        $this->add('PUT', $url, $action);
    }

    /**
     * POST Route
     * 
     * @param string $url The prefix of url
     * @param Closure|string
     */
    public function post(string $url, $action)
    {
        $this->add('POST', $url, $action);
    }

    /**
     * PATCH Route
     * 
     * @param string $url The prefix of url
     * @param Closure|string
     */
    public function patch(string $url, $action)
    {
        $this->add('PATCH', $url, $action);
    }

    /**
     * DELETE Route
     * 
     * @param string $url The prefix of url
     * @param Closure|string
     */
    public function delete(string $url, $action)
    {
        $this->add('DELETE', $url, $action);
    }

   /**
    * Add route manager to router
    * 
    * @param string $url Url prefix
    * @param Closure|string $action Route action
    * @return void
    */
    private function add(string $method, string $url, $action)
    {
        $this->addRoute(new Route($method, $url, $action));
    }
}