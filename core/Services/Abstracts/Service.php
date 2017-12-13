<?php

namespace Core\Services\Abstracts;

use Core\Bootstrapers\Application;

abstract class Service
{
    /**
     * Service identifier name
     * 
     * @var string
     */
    public static $name;

    /**
     * Service booted class
     * 
     * @var mixed
     */
    protected static $service;

    /**
     * Boot the aplication service
     *
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    abstract public static function boot(Application $app);

    /**
     * Get service class
     * 
     * @return mixed
     */
    public static function service()
    {
        return self::$service;
    }
}