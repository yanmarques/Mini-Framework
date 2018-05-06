<?php

namespace Core\Stack;

use Core\Interfaces\Stack\ItemInterface;
use Core\Reflector\Reflector;
use Core\Stack\Traits\MagicStack;

class Item implements ItemInterface
{
    use MagicStack;

    /**
     * Value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Class constructor.
     *
     * @param mixed                       $value      Item value
     * @param Core\Collections\Collection $collection
     *
     * @return Core\Collections\Item
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * When item is called as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    public function __call($name, $params)
    {
        $reflector = Reflector::bind($this->value);
        $reflector->callMethod($name, $params);
    }
}
