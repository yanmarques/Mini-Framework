<?php

namespace Core\Observer;

abstract class AbstractObserver
{
    /**
     * Call observer by it's name
     * 
     * @param string $name Observer name
     * @return void
     */
    abstract public function call($name);
}