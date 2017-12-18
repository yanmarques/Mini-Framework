<?php

namespace Core\Sessions;

use Core\Crypt\Crypter;

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
     * Session stack with params
     *
     * @var Core\Sessions\SessionStack
     */
    private $session;

    /**
     * Constructor of class
     *
     * @return Core\Sessions\SessionManager
     */
    public function __construct()
    {
        // $this->bootSession();
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

    /**
     * Get session stack
     *
     * @return Core\Sessions\SessionStack
     */
    public function stack()
    {
        return $this->session;
    }

    /**
     * Flash sessions to php SESSION
     *
     * @return void
     */
    public function flash()
    {
        $this->session->each(function ($value, $key) {
            $_SESSION[$key] = $value;
        });
    }

    /**
     * Start session
     *
     * @return void
     */
    private function bootSession()
    {
        session_start();
        $this->session = new SessionStack($_SESSION);
    }
}
