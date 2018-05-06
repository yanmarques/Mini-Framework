<?php

namespace Core\Http;

use Core\Stack\Stack;

class HeadersStack extends Stack
{
    /**
     * Constructor of class.
     *
     * @param array $args Array to stack
     *
     * @return Core\Http\HeadersStack
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
    }
}
