<?php

namespace Core\Database;

use Core\Exceptions\Database\DriverNotSupportedException;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Interfaces\Database\ManagerInterface;
use Doctrine\DBAL\DriverManager;

class ConnectionManager implements ManagerInterface
{
    /**
     * Get all supported drivers.
     *
     * @return array
     */
    public function getSupported()
    {
        return array_keys($this->drivers());
    }

    /**
     * Resolve doctrine connection using parameters.
     *
     * @param string                                            $driver Driver name
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app    Application
     *
     * @return Doctrine\DBAL\Connection
     */
    public function getConnection(string $driver, ApplicationInterface $app)
    {
        $drivers = $this->drivers();
        if (!isset($drivers[$driver])) {
            throw new DriverNotSupportedException("Doctrine driver [$driver] is not supported.");
        }

        // Connection parameters
        $params = [
            'driverClass' => new $drivers[$driver](),
            'dbname'      => $app->database()->dbname,
            'host'        => $app->database()->host,
            'port'        => $app->database()->port,
            'user'        => $app->database()->user,
            'password'    => $app->database()->password,
        ];

        return DriverManager::getConnection($params);
    }

    /**
     * Doctrine supported drivers.
     *
     * @return array
     */
    private function drivers()
    {
        return [
            'mysql'  => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
            'sqlite' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
            'pgsql'  => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
        ];
    }
}
