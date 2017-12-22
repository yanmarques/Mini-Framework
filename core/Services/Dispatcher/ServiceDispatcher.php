<?php

namespace Core\Services\Dispatcher;

use Core\Interfaces\Services\Dispachable;
use Core\Reflector\Reflector;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

class ServiceDispatcher implements Dispachable
{
    /**
     * Dispatch service class
     *
     * @param mixed $service Service class to dispatch
     * @return mixed
     */
    public static function dispatch(ApplicationInterface $app, $service)
    {
        $reflectorService = Reflector::bind($service);

        return [
            $reflectorService->getProperty('name') => $reflectorService->callStaticMethod('boot', [$app])
        ];
    }
}
