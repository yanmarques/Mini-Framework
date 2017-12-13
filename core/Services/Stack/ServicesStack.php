<?php

namespace Core\Services\Stack;

use Core\Stack\Stack;

class ServicesStack
{
    /**
     * Stack with started services
     * 
     * @var Core\Stack\Stack
     */
    private $services;

    /**
     * Constructor of class
     * 
     * @param Core\Stack\Stack
     * @return Core\Services\ServicesStack
     */
    public function __construct(Stack $services)
    {
        $this->services = $services;
    }

    /**
     * Get all services name
     * 
     * @return array
     */
    private function getServicesName()
    {
        return $this->services->keys();
    }

    /**
     * Dinamically access service through __call method
     * 
     * @throws Exception
     * 
     * @param string $name Name of method called
     * @param mixed $arguments Arguments passed to method
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ( in_array($name, $this->getServicesName()) ) {
            return $this->services->get($name);
        }

        throw new \Exception("Call to undefined service [$name]");
    }
}