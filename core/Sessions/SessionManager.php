<?php

namespace Core\Sessions;

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
     * Supported drivers name
     * 
     * @var array
     */
    private static $handlers = [
        'file' => FileSessionHandler::class,
    ];

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
     * Indicates wheter driver name is supported by application
     * 
     * @param string $driver Driver name
     * @return bool
     */
    public static function supported(string $driver)
    {
        return in_array($driver, array_keys(static::$handlers));
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
        $driver = $this->getDriverFromConfiguration();
        $this->session = Session::boot($driver);
    }

    /**
     * Return a handler instance from session configuration file
     * 
     * @return \SessionHandlerInterface
     */
    protected function getDriverFromConfiguration()
    {
        if ( ! static::supported($driver = $this->app->session()->session_driver) ) {
            throw new \Exception("Session driver [$driver] not supported.");
        }

        return new static::$handlers[$driver]($this->app->fileHandler(), 
            base_dir($this->app->session()->path), 
            $this->app->session()->session_lifetime,
            $this->app->session()->encrypt);
    }

    /**
     * Set Cross Site Request Forgery token for session
     *
     * @return void
     */
    private function setCSRFToken()
    {
        // Token already set
        if ( $this->session->has('CSRFToken') ) {
            return;
        }

        if ( isset($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], ['GET', 'HEAD', 'OPTIONS']) ) {
            $token = new CSRFToken;
            $this->session->set('CSRFToken', $token->serialize());
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
