<?php

namespace Core\Interfaces\Stack;

interface StackInterface
{
    /**
     * Get array stack
     *
     * @return array
     */
    public function all();

    /**
     * Add an element to stack
     *
     * @param mixed $value Value to add
     * @param string|null $key Key to add this value in
     */
    public function add($value, $key = null);

    /**
     * Iterate recursivaly into stack and execute a function on each row
     * return a new stack with values
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return string|Core\Stack\Stack
     */
    public function map(callable $callback);

    /**
     * Iterate recursivaly into stack and execute a function with next row on second parameter and
     * return a new stack with values
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return string|Core\Stack\Stack
     */
    public function mapWithNext(callable $callback);

    /**
     * Iterate recursivaly into stack and execute a function on each row
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return array|null
     */
    public function each(callable $callback);

    /**
     * Iterate recursivaly into stack and remove element if $callback returns true
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return Core\Stack\Stack|null
     */
    public function reject(callable $callback);

    /**
     * Remove an element from stack with it`s key
     *
     * @param string|int $key Key of element to remove
     * @return array
     */
    public function pull($key);

    /**
     * stack has a given key
     *
     * @param string|int $key Key to check
     * @return bool
     */
    public function has($key);


    /**
     * Get an element by it key
     *
     * @param string|int $key Key to find the element
     * @param mixed|null
     */
    public function get($key);

    /**
     * Get the length of the stack
     *
     * @return int
     */
    public function length();

    /**
     * Get all keys from stack
     *
     * @return array
     */
    public function keys();

    /**
     * Get the first item of stack
     *
     * @return mixed|null
     */
    public function first();

    /**
     * Get the last item of stack
     *
     * @return mixed|null
     */
    public function last();

    /**
     * Check wheter stack is empty
     *
     * @return bool
     */
    public function empty();

    /**
     * When stack is called as string
     *
     * @return string
     */
    public function __toString();
}
