<?php

namespace Core\Interfaces\Services;

use Core\Bootstrapers\Application;

interface Dispachable
{
    /**
     * Dispatch a given class
     *
     * @param $service Service to dispatch
     * @return mixed
     */
    public static function dispatch(Application $app, $service);
}