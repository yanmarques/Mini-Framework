<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Support\Config;
use Core\Bootstrapers\Application;

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
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    public static function boot(Application $app)
    {
        return Config::boot($app);
    }
}