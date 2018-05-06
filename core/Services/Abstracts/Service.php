<?php

namespace Core\Services\Abstracts;

use Core\Interfaces\Bootstrapers\ApplicationInterface;

abstract class Service
{
    /**
     * Service identifier name.
     *
     * @var string
     */
    public static $name;

    /**
     * Service booted class.
     *
     * @var mixed
     */
    protected static $service;

    /**
     * Boot the aplication service.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     *
     * @return mixed
     */
    abstract public static function boot(ApplicationInterface $app);

    /**
     * Get service class.
     *
     * @return mixed
     */
    public static function service()
    {
        return self::$service;
    }
}
