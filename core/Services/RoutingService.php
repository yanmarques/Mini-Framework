<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Routing\Router;
use Core\Routing\RouteResolver;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

class RoutingService extends Service
{
     /**
     * Service identifier name
     *
     * @var string
     */
    public static $name = 'routing';

    /**
     * Boot the aplication service
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        $route = new Router;

        include $app->routesDir().'app.php';

        return new RouteResolver($app, $route);
    }
}
