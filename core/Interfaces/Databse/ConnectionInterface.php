<?php

namespace Core\Interfaces\Database;

interface ConnectionInterface
{
     /**
     * Get singleton connection from instance
     * 
     * @return resource
     */
    public function getConnection();

    /**
     * Get database name
     * 
     * @return string
     */
    public function getDbname();

    /**
     * Get database host
     * 
     * @return string
     */
    public function getHost();

    /**
     * Get database port
     * 
     * @return int
     */
    public function getPort();

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
    public function buildString();
}