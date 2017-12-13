<?php

namespace Core\Stack;

use Core\Interfaces\Stack\ItemInterface;
use Core\Stack\Traits\MagicStack;

class Item implements ItemInterface
{
    use MagicStack;

    /**
     * Value
     *
     * @var mixed
     */
    private $value;

    /**
     * Class constructor
     *
     * @param mixed $value Item value
     * @param Core\Collections\Collection $collection
     * @return Core\Collections\Item
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * When item is called as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
