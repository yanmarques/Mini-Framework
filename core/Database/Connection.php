<?php

namespace Core\Database;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Interfaces\Database\ConnectionInterface;
use Core\Interfaces\Database\ManagerInterface;
use Doctrine\DBAL\DBALException;

class Connection implements ConnectionInterface
{
    /**
     * Connection singleton is booted.
     *
     * @var bool
     */
    private static $booted = false;

    /**
     * Connection instance.
     *
     * @var Core\Database\Connection
     */
    private static $instance;

    /**
     * Database driver.
     *
     * @var string
     */
    private $driver;

    /**
     * Connection manager.
     *
     * @var Core\Interfaces\Database\ManagerInterface
     */
    private $manager;

    /**
     * Doctrine connection class.
     *
     * @var Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * ApplicationInterface.
     *
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;

        $this->useDefaultManager();

        $driver = $app->database()->driver;

        // Driver is not supported
        if (!$this->supported($driver)) {
            throw new \RuntimeException("Database driver [$driver] are not supported.");
        }

        $this->driver = $driver;
    }

    /**
     * Boot database singleton instance.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     *
     * @return Core\Database\Connection
     */
    public static function boot(ApplicationInterface $app = null)
    {
        // Database not booted
        if (!static::$booted) {
            static::$booted = true;
            static::$instance = new static($app);
        }

        return static::$instance;
    }

    /**
     * Verify wheter driver is supported by application.
     *
     * @param string $driver Driver name
     *
     * @return bool
     */
    private function supported(string $driver)
    {
        return in_array($driver, $this->getManager()->getSupported());
    }

    /**
     * Resolve connection class.
     *
     * @return void
     */
    public function connect()
    {
        // Get an Doctrine\DBAL\Connection instance
        $this->connection = $this->getManager()->getConnection($this->driver, $this->app);
        $this->connection->connect();
    }

    /**
     * Set default connection manager.
     *
     * @return void
     */
    public function useDefaultManager()
    {
        $this->setManager(new ConnectionManager());
    }

    /**
     * Get connection instance.
     *
     * @return mixed
     */
    public function getConnection()
    {
        // Not connected yet, use connect method to connect
        if ($this->connection == null) {
            throw new ConnectionException('No connection available.');
        }

        // You are not connected on database
        if (!$this->connection->isConnected()) {
            throw new ConnectionException('System is not connected.');
        }

        return $this->connection;
    }

    /**
     * Prepares a SQL statement.
     *
     * @param string $statement The SQL string to prepare
     *
     * @return Core\Database\Statement The prepared statement
     */
    public function prepare(string $statement, array $bindings)
    {
        try {
            $stmt = Statement::boot($statement, $bindings, $this);
        } catch (\Exception $ex) {
            throw DBALException::driverExceptionDuringQuery($this->connection->getDriver(), $ex, $statement);
        }

        return $stmt;
    }

    /**
     * Get database name.
     *
     * @return string
     */
    public function getDbname()
    {
        return $this->app->database()->dbname;
    }

    /**
     * Get database host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->app->database()->host;
    }

    /**
     * Get database port.
     *
     * @return string
     */
    public function getPort()
    {
        return $this->app->database()->port;
    }

    /**
     * Get connection user.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->app->database()->user;
    }

    /**
     * Get connection password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->app->database()->password;
    }

    /**
     * Set connection manager.
     *
     * @param Core\Interfaces\Database\ManagerInterface $manager Manager to create connections
     *
     * @return void
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get connection manager.
     *
     * @return Core\Interfaces\Database\ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}
