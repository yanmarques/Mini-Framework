<?php

namespace Core\Database;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Reflector\Reflector;

class Connection 
{
    /**
     * Connection singleton is booted
     * 
     * @var bool
     */
    private static $booted;

    /**
     * Connection instance
     * 
     * @var Core\Database\Connection
     */
    private static $instance;

    /**
     * Database driver
     * 
     * @var string
     */
    private $driver;

    /**
     * Connection class
     * 
     * @var mixed
     */
    private $connection;

    /**
     * ApplicationInterface
     * 
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface $app
     */
    private $app;   

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
        
        $driver = $app->database()->get('driver');

        // Driver is not supported
        if ( ! static::supported($driver) ) {
            throw new \RuntimeException("Database driver [$driver] are not supported.");
        }

        $this->driver = $driver;
    }

    /**
     * Boot database singleton instance
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return Core\Database\Connection
     */
    static function boot(ApplicationInterface $app)
    {
        // Database not booted
        if ( ! static::$booted ) {
            static::$instance = new self($app);
        }

        return static::$instance;
    }

    /**
     * Verify wheter driver is supported by application
     * 
     * @param string $driver Driver name
     * @return bool
     */
    static function supported(string $driver)
    {
        return in_array($driver, array_keys(static::drivers()));
    }

    /**
     * Resolve connection class
     * 
     * @return void
     */
    public function connect()
    {
        $connection = static::drivers()[$this->driver];
        $connection = Reflector::bind($connection);

        if ( ! $connection->implementsInterface('Core\Interfaces\Database\ConnectionInterface') ) {
            throw new \RuntimeException("Connection class [{$connection->getName()}]");
        }

        $this->connection = $connection->callStaticMethod("boot", [$this->app])->getConnection(); 
    }

    /**
     * Get all drivers name supported
     * 
     * @return array
     */
    private static function drivers()
    {
        return [
            'pgsql' => \Core\Database\Postgresql::class,
            'mysql' => \Core\Database\Mysql::class
        ];
    }
}