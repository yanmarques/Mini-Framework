<?php

namespace Core\Interfaces\Services;

use Core\Interfaces\Bootstrapers\ApplicationInterface;

interface Dispachable
{
    /**
     * Dispatch a given class.
     *
     * @param $service Service to dispatch
     *
     * @return mixed
     */
    public static function dispatch(ApplicationInterface $app, $service);
}
