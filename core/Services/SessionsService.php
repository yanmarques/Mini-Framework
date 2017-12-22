<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Sessions\SessionManager;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

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
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        return SessionManager::boot($app);
    }
}