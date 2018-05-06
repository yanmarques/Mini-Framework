<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Reflector\Reflector;

class Middleware
{
    /**
     * Middleware is booted.
     *
     * @var bool
     */
    private static $booted = false;

    /**
     * Middleware instance.
     *
     * @var Core\Routing\Middleware
     */
    private static $instance;

    /**
     * ApplicationInterface.
     *
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    /**
     * Request to parse middleware.
     *
     * @var Core\Http\Request
     */
    private $request;

    /**
     * Middleware interface.
     *
     * @var string
     */
    private $interface = 'Core\\Interfaces\\Http\\MiddlewareInterface';

    public function __construct(ApplicationInterface $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Boot middleware on request.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app     ApplicationInterface
     * @param Core\Http\Request                                 $request
     *
     * @return Core\Routing\Middleware
     */
    public static function boot(ApplicationInterface $app, Request $request)
    {
        if (!static::$booted) {
            static::$instance = new self($app, $request);
        }

        return static::$instance;
    }

    /**
     * Run globals and web middlewares.
     *
     * @param array $middlewares Array with web middlewares on route
     *
     * @return Core\Http\Request
     */
    public function run(array $middlewares)
    {
        $this->runGlobals();
        $this->runWeb($middlewares);

        return $this->request;
    }

    /**
     * Run globals middlewares.
     *
     * @return void
     */
    private function runGlobals()
    {
        // Global middlewares
        $global = $this->app->middleware()->global;

        // Iterate into each middleware and call apply method
        // Middlewares must implements Core\Interfaces\Http\MiddlewareInterface
        $global->each(function ($middleware) {
            $this->apply($middleware);
        });
    }

    /**
     * Run web middlewares on route.
     *
     * @param array $middlewares Middlewares name
     *
     * @return void
     */
    private function runWeb(array $middlewares)
    {
        // Web middlewares
        $web = $this->app->middleware()->web;

        // Iterate on each route an apply middleware if registered on middlewares configuration
        foreach ($middlewares as $middleware) {

            // Middleware name was not found
            if (!$web->get($middleware)) {
                throw new \RuntimeException("Invalid middleware [$middleware]");
            }

            $this->apply($web->get($middleware));
        }
    }

    /**
     * Apply middleware class to request.
     *
     * @param Core\Interfaces\Http\MiddlewareInterface $middleware Middleware to apply
     *
     * @return void
     */
    private function apply($middleware)
    {
        $class = Reflector::bind($middleware);

        if (!$class->implementsInterface($this->interface)) {
            throw new \RuntimeException("Middleware [$middleware] must implements [$this->interface]");
        }

        // Call method to apply middleware
        $this->request = $class->callMethod('apply', [$this->request]);
    }
}
