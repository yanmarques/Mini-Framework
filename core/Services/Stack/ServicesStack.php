<?php

namespace Core\Services\Stack;

use Core\Stack\Stack;

class ServicesStack extends Stack
{
    /**
     * Constructor of class.
     *
     * @param Core\Stack\Stack
     *
     * @return Core\Services\ServicesStack
     */
    public function __construct($services = [])
    {
        parent::__construct($services);
    }

    /**
     * Dinamically access service through __call method.
     *
     *
     * @param string $name      Name of method called
     * @param mixed  $arguments Arguments passed to method
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->keys())) {
            return $this->get($name);
        }

        throw new \Exception("Call to undefined service [$name]");
    }
}
