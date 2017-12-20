<?php

namespace Core\Bootstrapers;

use Core\Interfaces\Services\Dispachable;
use Core\Reflector\Reflector;
use Core\Bootstrapers\Application;

class ServiceDispatcher implements Dispachable
{
    /**
     * Dispatch service class
     *
     * @param mixed $service Service class to dispatch
     * @return mixed
     */
    public static function dispatch(Application $app, $service)
    {
        $reflectorService = Reflector::bind($service);

        return [
            $reflectorService->getProperty('name') => $reflectorService->callStaticMethod('boot', [$app])
        ];
    }
}
