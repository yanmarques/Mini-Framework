<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Sessions\SessionManager;
use Core\Bootstrapers\Application;

class SessionsService extends Service
{   
    /**
     * Service identifier name
     * 
     * @var string
     */
    public static $name = 'session';

    /**
     * Boot the aplication service
     *
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    public static function boot(Application $app)
    {
        return SessionManager::boot($app);
    }
}