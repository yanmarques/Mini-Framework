<?php

namespace Core\Stack\Traits;

use Core\Stack\Stack;

trait MagicStack
{
    /**
     * Dinamically access collection attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($value)
    {
        // Value exists on collection
        if ($value = $this->get($value)) {
            return is_array($value) ?
                    new Stack($value) : $value;
        }
    }
}
