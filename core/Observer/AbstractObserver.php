<?php

namespace Core\Observer;

abstract class AbstractObserver
{
    /**
     * Dispatch observer by it's name.
     *
     * @param string $name Observer name
     *
     * @return void
     */
    abstract public function dispatch($name);
}
