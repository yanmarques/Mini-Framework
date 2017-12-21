<?php

namespace Core\Database;

use Core\Interfaces\Database\ConnectionInterface;
use Core\Bootstrapers\Application;
use Core\Database\Traits\PDODriver;

class Postgresql implements ConnectionInterface
{
    use PDODriver;

    /**
     * Postgresql singleton is booted
     * 
     * @var bool
     */
    private static $booted;

    /**
     * Postgresql instance
     * 
     * @var Core\Database\Connection
     */
    private static $instance; 

    /**
     * Application
     * 
     * @var Core\Bootstrapers\Application $app
     */
    private $app;   

    /**
     * Connection
     * 
     * @var resource
     */
    protected $connection;  

    /**
     * PDO string configuration to connect
     * 
     * @var string
     */
    protected $dbname;

    /**
     * PDO host database configuration
     * 
     * @var string
     */
    protected $host;

    /**
     * PDO port database configuration
     * 
     * @var string
     */
    protected $port;

    /**
     * PDO user configuration
     * 
     * @var string
     */
    protected $user;

    /**
     * PDO password configuration
     * 
     * @var string
     */
    protected $password;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->dbname = $this->app->database()->get('dbname');
        $this->host = $this->app->database()->get('host');
        $this->port = $this->app->database()->get('port');
        $this->user = $this->app->database()->get('user');
        $this->password = $this->app->database()->get('password');
        $this->connection = $this->resolveConnection();
    }

    /**
     * Boot postresql connection singleton instance
     * 
     * @param Core\Bootstrapers\Application $app
     * @return Core\Database\Connection
     */
    static function boot(Application $app)
    {
        // Database not booted
        if ( ! static::$booted ) {
            static::$instance = new self($app);
        }

        return static::$instance;
    }

    /**
     * Get singleton connection from instance
     * 
     * @return resource
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get database name
     * 
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * Get database host
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get database port
     * 
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Build connection string from configuration
     * 
     * @param string $host Database host
     * @param int $port Port where database is connected
     * @param string $dbname Dababase name
     * @param string $user User name
     * @param string $password Password to dabatase
     * @return string
     */
    public function buildString()
    {
        return "pgsql:dbname={$this->dbname};host={$this->host};port={$this->port}";
    }
}