<?php

namespace Core\Database\Traits;

trait HasActionEvents
{
    /**
     * Named actions to execute
     * 
     * @var array
     */
    protected static $actions = [
        'creating' => [],
        'created' => [],
        'updating' => [],
        'updated' => []
    ];

    /**
     * Add function to "creating" event
     * 
     * @param \Closure $callback Function to execute
     * @return void
     */
    protected static function creating(\Closure $callback)
    {
        static::$actions['creating'][] = $callback;
    }

    /**
     * Add function to "created" event
     * 
     * @param \Closure $callback Function to execute
     * @return void
     */
    protected static function created(\Closure $callback)
    {
        static::$actions['created'][] = $callback;
    }

    /**
     * Add function to "updating" event
     * 
     * @param \Closure $callback Function to execute
     * @return void
     */
    protected static function updating(\Closure $callback)
    {
        static::$actions['updating'][] = $callback;
    }

    /**
     * Add function to "updated" event
     * 
     * @param \Closure $callback Function to execute
     * @return void
     */
    protected static function updated(\Closure $callback)
    {
        static::$actions['updated'][] = $callback;
    }

    /**
     * Dispatch a registered event
     * 
     * @param string $action Action name
     * @param mixed $argument Argument to pass as function parameter
     * @return void
     */
    private function fireEvent(string $action, $argument=null)
    {
        // Event exist
        if ( ! isset(static::$actions[$action]) ) {
            throw new \Exception("Call to invalid named action event [$action]");
        }

        $closures = static::$actions[$action];

        // Argument to pass as parameter to closure
        $argument = $argument ?: $this;

        foreach($closures as $clousure) {
            call_user_func_array($clousure, [&$argument]);
        }
    }
}