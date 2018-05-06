<?php

namespace Core\Interfaces\Stack;

interface ItemInterface
{
    /**
     * When item is called as string.
     *
     * @return string
     */
    public function __toString();
}
