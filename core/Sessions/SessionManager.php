<?php

namespace Core\Sessions;

class SessionManager
{   
    /**
     * Session booted
     * 
     * @var bool
     */
    private static $booted;

    /**
     * Single instance of session manager
     * 
     * @var Core\Sessions\SessionManager
     */
    private static $instance;

    /**
     * Constructor of class
     * 
     * @return Core\Sessions\SessionManager
     */
    public function __construct()
    {
        // Start session and resolve drivers
        $this->bootSession();
    }

    /**
     * Boot session manager
     * 
     * @return Core\Sessions\SessionManager
     */
    public static function boot()
    {
        if ( ! self::$booted ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function bootSession()
    {
        session_start();
    }
}