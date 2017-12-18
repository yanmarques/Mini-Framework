<?php

namespace Core\Sessions;

use Core\Crypt\Crypter;
use Core\Bootstrapers\Application;

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
     * Application class
     *
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * Constructor of class
     *
     * @param Core\Bootstrapers\Application
     * @return Core\Sessions\SessionManager
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->bootSession();
        $this->setCSRFToken();
    }

    /**
     * Boot session manager
     *
     * @param Core\Bootstrapers\Application
     * @return Core\Sessions\SessionManager
     */
    public static function boot(Application $app)
    {
        if ( ! self::$booted ) {
            self::$instance = new self($app);
        }

        return self::$instance;
    }

    /**
     * Get session stack with values
     * 
     * @return Core\Sessions\SessionStack
     */
    public function stack()
    {
        return $this->session;
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

    /**
     * Set Cross Site Request Forgery token for session
     * 
     * @return Core\Sessions\SessionManager
     */
    private function setCSRFToken()
    {
        $this->session->set('CSRFToken', Crypter::random(64));
    }

    public function __get($name)
    {
        if ( $value = $this->session->get($name) ) {
            return $value;
        }

        return null;
    }
}
