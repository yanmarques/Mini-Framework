<?php

namespace Core\Observer;

use Core\Reflector\Reflector;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

class ObserverReflector extends AbstractObserver
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
     * Observers stack
     * 
     * @var Core\Stack\Stack
     */
    private $observers;

    /**
     * Constructor of class
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * 
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
        $this->observers = $app->observers();
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
     * Dispatcher observer by it's name
     * 
     * @param string $name Observer name
     * @return void
     */
    public function dispatch($name)
    {
        if ( ! $this->observers->get($name) ) {
            throw new \Exception('Observer does not exist.');
        }

        $reflector = Reflector::bind($this->observers->get($name));

        // Observer uses interface
        if ( ! $reflector->implementsInterface(\Core\Interfaces\Observer\ObserverInterface::class) ) {
            throw new \Exception("Observer must implement {\Core\Interfaces\Observer\ObserverInterface::class}");
        }

        $reflector->callMethod('handle');
    }
}