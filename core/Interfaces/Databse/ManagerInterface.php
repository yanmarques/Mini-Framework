<?php

namespace Core\Interfaces\Database;

use Core\Interfaces\Bootstrapers\ApplicationInterface;

interface ManagerInterface
{
    /**
     * Get all supported drivers.
     *
     * @return array
     */
    public function getSupported();

    /**
     * Resolve doctrine connection using parameters.
     *
     * @param string                                            $driver Driver name
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app    Application
     *
     * @return Doctrine\DBAL\Connection
     */
    public function getConnection(string $driver, ApplicationInterface $app);
}
