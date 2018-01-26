<?php

namespace Core\Interfaces\Database;

interface ConnectionInterface
{
     /**
     * Get connection instance
     * 
     * @return mixed
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
     * @return string
     */
    public function getPort();

    /**
     * Get connection user
     * 
     * @return string
     */
    public function getUser();

    /**
     * Get connectio password
     * 
     * @return string
     */
    public function getPassword();

    /**
     * Set connection manager
     * 
     * @param Core\Interfaces\Database\ManagerInterface $manager Manager to create connections
     * @return void
     */
    public function setManager(ManagerInterface $manager);

    /**
     * Get connection manager
     * 
     * @return Core\Interfaces\Database\ManagerInterface
     */
    public function getManager();
}