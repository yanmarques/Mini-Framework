<?php

namespace Core\Sessions;

use Core\Stack\Stack;

class SessionStack extends Stack
{
    /**
     * Constructor of class.
     *
     * @param array $args Array with params
     *
     * @return Core\Sessions\SessionStack
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
    }

    /**
     * Set a session key/value and add to stack.
     *
     * @param string $key   Key of session
     * @param mixed  $value Key value
     *
     * @return Core\Sessions\SessionStack
     */
    public function set(string $key, $value)
    {
        $this->add($value, $key);
        $_SESSION[$key] = $value;
    }

    /**
     * Unset session key.
     *
     * @param string $key Key of session
     *
     * @return void
     */
    public function unset(string $key)
    {
        $this->pull($key);
        unset($_SESSION[$key]);
    }
}
