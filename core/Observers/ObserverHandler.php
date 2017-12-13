<?php

namespace Core\Observers;

abstract class ObserverHandler
{
    /**
     * Handle an Observer call
     * 
     * @param mixed $class Observer class
     * @param array $params Params to pass on Observer 
     * @return void
     */
    abstract public static function handle($class, array $params);
}