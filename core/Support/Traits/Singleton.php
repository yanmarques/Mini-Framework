<?php

namespace Core\Support\Traits;

trait Singleton
{
    /**
     * Singleton instance of class
     * 
     * @var mixed
     */
    private static $instance;

    /**
     * Indicate wheter singleton is booted
     * 
     * @var bool
     */
    private static $booted = false;

    /**
     * Boot class using singleton design pattern, and chain to be able to call class functions
     * like "Class::boot()->example"
     * 
     * @return mixed
     */
    static function boot()
    {
        // Singleton not booted
        if ( ! static::$booted ) {
            static::$instance = new static(...func_get_args());
            static::$booted = true;
        }

        return static::$instance;
    }

    /**
     * Return singleton instance of class.
     * 
     * @return mixed
     */
    static function instance()
    {  
        if ( static::$booted ) {
            return static::$instance;
        }

        return null;
    }
}