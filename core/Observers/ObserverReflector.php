<?php

namespace Core\Observers;

use Core\Reflector\Reflector;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

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
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * 
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Boot observer
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return Core\Observers\ObserverReflector
     */
    public static function boot(ApplicationInterface $app)
    {
        if ( ! self::$booted ) {
            self::$instance = new self($app);
        }

        return self::$instance;
    }

    /**
     * Get current appplication
     * 
     * @return Core\Interfaces\Bootstrapers\ApplicationInterface
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