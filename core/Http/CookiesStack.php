<?php

namespace Core\Http;

use Core\Stack\Stack;

class CookiesStack extends Stack
{
    /**
     * Constructor of class.
     *
     * @param array $args Array to stack
     *
     * @return Core\Http\CookiesStack
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
    }
}
