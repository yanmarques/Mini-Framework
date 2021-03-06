<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Observer\ObserverReflector;

class ObserverService extends Service
{   
    /**
     * Service identifier name
     * 
     * @var string
     */
    public static $name = 'observer';

    /**
     * Boot the aplication service
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        return ObserverReflector::boot($app);
    }
}