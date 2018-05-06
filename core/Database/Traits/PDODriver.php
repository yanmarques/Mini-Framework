<?php

namespace Core\Database\Traits;

use Core\Exceptions\Database\ConnectionException;

trait PDODriver
{
    /**
     * Create database connection from configuration.
     *
     * @return resource
     */
    private function resolveConnection()
    {
        try {
            $this->connection = new \PDO(
                $this->buildString(),
                $this->user,
                $this->password
            );
        } catch (\PDOException $e) {
            throw new ConnectionException($e->getMessage());
        }
    }
}
