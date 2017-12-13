<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Routing\Router;
use Core\Routing\RouteResolver;
use Core\Bootstrapers\Application;

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
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    public static function boot(Application $app)
    {
        $route = new Router;
        
        include $app->baseDir() .DIRECTORY_SEPARATOR. '/routes/app.php';

        return new RouteResolver($route);
    }
}