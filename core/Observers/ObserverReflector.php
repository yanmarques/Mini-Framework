<?php

namespace Core\Observers;

use Core\Reflector\Reflector;
use Core\Bootstrapers\Application;

class ObserverReflector extends ObserverHandler
{
    /**
     * Observer is booted
     * 
     * @var bool
     */
    protected static $booted = false;

    /**
     * Singleton instance of class
     * 
     * @var Core\Observers\ObserverReflector
     */
    private static $instance;

    /**
     * Constructor of class
     * 
     * @param Core\Bootstrapers\Application $app
     * 
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Boot observer
     * 
     * @param Core\Bootstrapers\Application $app
     * @return Core\Observers\ObserverReflector
     */
    public static function boot(Application $app)
    {
        if ( ! self::$booted ) {
            self::$instance = new self($app);
        }

        return self::$instance;
    }

    /**
     * Get current appplication
     * 
     * @return Core\Bootstrapers\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

     /**
     * Handle an Observer call
     * 
     * @param mixed $class Observer class
     * @param array $params Params to pass on Observer 
     * @return void
     */
    public static function handle($class, ...$params)
    {   
        $reflector = Reflector::bind($class);

        return $reflector->callMethod('');
    }
}