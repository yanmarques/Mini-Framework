<?php

namespace Core\Sessions;

use Core\Stack\Stack;

class SessionStack extends Stack
{
    /**
     * Constructor of class
     *
     * @param array $args Array with params
     * @return Core\Sessions\SessionStack
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
    }
}
