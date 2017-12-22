<?php

namespace Core\Sessions;

use Core\Crypt\Crypter;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

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
     * ApplicationInterface class
     *
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    /**
     * Constructor of class
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface
     * @return Core\Sessions\SessionManager
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
        $this->bootSession();
        $this->setCSRFToken();
    }

    /**
     * Boot session manager
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface
     * @return Core\Sessions\SessionManager
     */
    public static function boot(ApplicationInterface $app)
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
        if ( in_array($_SERVER['SERVER_PROTOCOL'], ['GET', 'HEAD', 'OPTIONS']) ) {
            $this->session->set('CSRFToken', Crypter::random(64));
        }
    }

    public function __get($name)
    {
        if ( $value = $this->session->get($name) ) {
            return $value;
        }

        return null;
    }
}
