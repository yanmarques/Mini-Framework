<?php

namespace Core\Stack;

use Core\Interfaces\Stack\StackInterface;
use Core\Stack\Traits\MagicStack;

class Stack implements StackInterface
{
    use MagicStack;

    /**
     * Array containing data
     *
     * @var array
     */
    private $stack;

    public function __construct(array $args = [])
    {
        $this->stack = $args;
    }

    /**
     * Get array stack
     *
     * @return array
     */
    public function all()
    {
        return $this->stack;
    }

    /**
     * Add an element to stack
     *
     * @param mixed $value Value to add
     * @param string|null $key Key to add this value in
     */
    public function add($value, $key = null)
    {
        // Associative array
        if ( $this->isAssociative() ) {

            // Must have a key to add
            if ( ! $key ) throw new \Exception('The parameter $key must not be null');

            $this->stack[$key] = $value;
        }

        // Sequential array
        else {
            $this->stack[$this->empty() ? 0 : $this->length()] = $value;
        }

        return $this;
    }

    /**
     * Collpase all stack values
     *
     * @return Core\Stack\Stack
     */
    public function collapse()
    {
        return $this->internalCollapse();
    }

    /**
     * Iterate recursivaly into stack and execute a function on each row
     * return a new stack with values
     *
     * @return Core\Stack\Stack
     */
    public function map(callable $callback)
    {
        return $this->internalMap($callback);
    }

    /**
     * Iterate recursivaly into stack and execute a function on each row
     *
     * @return Core\Stack\Stack
     */
    public function each(callable $callback)
    {
        return $this->internalEach($callback);
    }


    /**
     * Iterate recursivaly into stack and execute a function with next row on second parameter and
     * return a new stack with values
     *
     * @return Core\Stack\Stack
     */
    public function mapWithNext(callable $callback)
    {
        return $this->internalMapWithNext($callback);
    }

    /**
     * Iterate recursivaly into stack and remove element if $callback returns true
     *
     * @return Core\Stack\Stack
     */
    public function reject(callable $callback)
    {
        return $this->internalReject($callback);
    }

    /**
     * Iterate recursivaly into stack and merge stack with given array
     *
     * @return Core\Stack\Stack
     */
    public function merge(array $args, bool $caseSensitive = false)
    {
        return $this->internalMerge($args, $caseSensitive);
    }

    /**
     * Iterate recursivaly into stack and execute a function on each row
     * return a new stack with values
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return string|Core\Stack\Stack
     */
    private function internalMap(callable $callback, $stack = null)
    {
        $newStack = $stack ?: [];

        // Iterate into stack
        $this->iterator(function ($value, $key) use (&$newStack, $callback) {

            // $value is an array
            if ( is_array($value) ) {

                // New stack receives the result of the new call to function
                // passing the array $value as $stack parameter
                $newStack[$key] = $this->internalMap($callback, $value);
            }

            // Just execute the callback
            else {
                $newStack[$key] = $callback($value, $key);
            }
        }, $stack ?: null);

        return $stack ?
                $newStack : new self($newStack);
    }

    /**
     * Iterate recursivaly into stack and execute a function with next row on second parameter and
     * return a new stack with values
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return string|Core\Stack\Stack
     */
    private function internalMapWithNext(callable $callback, $stack = null)
    {
        $newStack = $stack ?: [];

        // Iterate into stack
        $this->iteratorWithNext(function ($value, $next, $key) use (&$newStack, $callback) {

            // $value is an array
            if ( is_array($value) ) {

                // New stack receives the result of the new call to function
                // passing the array $value as $stack parameter
                $newStack[$key] = $this->internalMapWithNext($callback, $value);
            }

            // Just execute the callback
            else {

                $newStack[$key] = $callback($value, $next, $key);
            }
        }, $stack ?: null);

        return $stack ?
                $newStack : new self($newStack);
    }

    /**
     * Iterate recursivaly into stack and execute a function on each row
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return array|null
     */
    private function internalEach(callable $callback, $stack = null)
    {
        // Iterate into stack
        $this->iterator(function ($value, $key) use ($callback) {

            // $value is an array
            if ( is_array($value) ) {

                // New stack receives the result of the new call to function
                // passing the array $value as $stack parameter
                $this->internalMap($callback, $value);
            }

            // Just execute the callback
            else {
                $callback($value, $key);
            }
        }, $stack ?: null);

        return $stack ?: null;
    }

    /**
     * Iterate recursivaly into stack and remove element if $callback returns true
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param array|null $stack Used to dive deeply on stack
     * @return null|Core\Stack\Stack
     */
    private function internalReject(callable $callback, $stack = null)
    {
        $newStack = $stack ?: [];

        // Iterate into stack
        $this->iterator(function ($value, $key) use (&$newStack, $callback) {

            // $value is an array
            if ( is_array($value) ) {

                // New stack receives the result of the new call to function
                // passing the array $value as $stack parameter
               $newStack[$key] = $this->internalMap($callback, $value);
            }

            // Just execute the callback
            else {
                // Callback returns true
                if ( $callback($value, $key) ) $this->pull($key);

                else $newStack[$key] = $value;
            }
        }, $stack ?: null);

        return $stack ?
                $newStack : $this;
    }

    /**
     * Internal collapse function
     *
     * @param array|null $recursivelyStack Stack to run recursive
     * @return Core\Stack\Stack
     */
    private function internalCollapse(array $recursivelyStack = null)
    {
        $newStack = $recursivelyStack ?: [];

        // stack is associative
        if ( $this->isAssociative() ) {

            // Iterate into array and collapse each array side by side
            $this->iterator(function ($value, $key) use (&$newStack) {

                if ( is_array($value) ) {
                    $newStack[$key] = $this->internalCollapse($value);
                } else {
                    $newStack[$key] = $value;
                }

            }, $recursivelyStack);

            return $recursivelyStack ? $newStack : new self($newStack);
        }

        // stack is sequential
        // Iterate into each sequential key on stack
        foreach($this->keys() as $sequentialKey) {

            // Use iterator on each position on sequential key
            $this->iterator(function ($value, $key) use (&$newStack) {
                if ( is_array($value) ) {
                    $newStack[$key] = $this->internalCollapse($value);
                } else {
                    $newStack[$key] = $value;
                }

            }, $this->stack[$sequentialKey]);
        }

        return $recursivelyStack ? $newStack : new self($newStack);
    }

    /**
     * Merge array with stack array with case sensitive option
     *
     * @param array $args Array to merge
     * @param bool $caseSensitive Merge with caseSensitive
     * @param array|null $stack Use to deep on array
     * @return array|Core\Stack\Stack
     */
    public function internalMerge(array $args, bool $caseSensitive, array $stack = null)
    {
        $args = $stack ?: $args;

        foreach ($args as $mergeKey => $mergeValue) {

            // Iterate into stack
            $this->iterator(function ($value, $key) use ($mergeKey, $mergeValue, $args, $caseSensitive) {

                // Use case insensitive
                if ( ! $caseSensitive ) {
                    $keyEquals = strtolower($mergeKey) == strtolower($key);
                } else {
                    $keyEquals = $mergeKey == $key;
                }

                // Merge key equals key
                if ( $keyEquals ) {

                    // Merge argument row with current row
                    $this->stack[$key] = $mergeValue;
                }

                // Value is an array
                elseif ( is_array($value) ) {
                    $this->stack[$key] = $this->internalMerge($args, $caseSensitive, $value);
                } else {
                    $this->stack[$mergeKey] = $mergeValue;
                }

            }, $stack ?: null);

        }

        return $stack ? $args : $this;
    }

    /**
     * Remove an element from stack with it`s key
     *
     * @param string|int $key Key of element to remove
     * @return array
     */
    public function pull($key)
    {
        if ( ! $this->has($key) ) throw \Exception("[$key] not found on stack.");

        $element = $this->stack[$key];

        // Unset element from stack
        unset($this->stack[$key]);

        // Return element
        return $element;
    }

    /**
     * Stack has a given key
     *
     * @param string|int $key Key to check
     * @return bool
     */
    public function has($key)
    {
        return isset($this->stack[$key]);
    }

    /**
     * Get an element by it key
     *
     * @param string|int $key Key to find the element
     * @param mixed|null
     */
    public function get($key)
    {
        // Stack is associative try to get an element
        // by it's key
        if ( $this->isAssociative() ) {
            return $this->stack[$key];
        }

        // Stack is sequential try to get an element
        // by position
        return $this->getByPosition($key);
    }

    /**
     * Get the length of the stack
     *
     * @return int
     */
    public function length()
    {
        return count($this->stack);
    }

    /**
     * Get all keys from stack
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->stack);
    }

    /**
     * Get the first item of stack
     *
     * @return mixed|null
     */
    public function first()
    {
        return $this->getByPosition(0);
    }

    /**
     * Get the last item of stack
     *
     * @return mixed|null
     */
    public function last()
    {
        return $this->getByPosition($this->length() - 1);
    }

    /**
     * Check wheter stack is empty
     *
     * @return bool
     */
    public function empty()
    {
        return empty($this->stack);
    }

    /**
     * Get an instance of stack mixed
     *
     * @return Core\Stack\Stack
     */
    protected function getInstance()
    {
        return $this;
    }

    /**
     * Get an element of the stack by it's position
     *
     * @param int $position Position to get
     * @param mixed|null
     */
    private function getByPosition(int $position)
    {
        // It is an associative array
        if ( $this->isAssociative() ) {
            return $this->stack[$this->keys()[$position]];
        }

        // Return the position in case is set otherwise returns null
        return $this->stack[$position] ?? null;
    }

    /**
     * Check wheter an array is associative
     * Return false in case it is sequential
     *
     * @param array|null $stack Stack to check
     * @return bool
     */
    private function isAssociative(array $stack = null)
    {
        $stack = $stack ?: $this->stack;

        // stack is empty
        if ( $this->empty() ) {
            return false;
        }

        return array_keys($stack) !== range(0, $this->length() - 1);
    }

    /**
     * Iterate into the stack
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param null|Core\Stack\Stack $stack stack to interate into
     */
    private function iterator(callable $callback, $stack = null)
    {
        $stack = $stack ?: $this->stack;

        // stack is an array
        if ( is_array($stack) ) {
            foreach($stack as $key => $value)
            {
                $callback($value, $key);
            }
        }

        // stack might be an value
        else {
            $callback($stack);
        }
    }

    /**
     * Iterate into the stack
     *
     * @param callable $callback Function to execute with $key and $value params
     * @param null|Core\Stack\Stack $stack stack to interate into
     */
    private function iteratorWithNext(callable $callback, $stack = null)
    {
        $stack = $stack ?: $this->stack;

        // Stack is an array
        if ( is_array($stack) ) {
            foreach($stack as $key => $value)
            {
                $callback($value, $this->getByPosition($key + 1) ?: null, $key);
            }
        }

        // Stack might be an value
        else {
            $callback($stack);
        }
    }

    /**
     * When stack is called as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->stack;
    }
}
