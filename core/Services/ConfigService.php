<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Support\Config;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

class ConfigService extends Service
{   
    /**
     * Service identifier name
     * 
     * @var string
     */
    public static $name = 'config';

    /**
     * Boot the aplication service
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        return Config::boot($app);
    }
}