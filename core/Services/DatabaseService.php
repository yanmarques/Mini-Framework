<?php

namespace Core\Services;

use Core\Database\Connection;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Services\Abstracts\Service;

class DatabaseService extends Service
{
    /**
     * Service identifier name.
     *
     * @var string
     */
    public static $name = 'database';

    /**
     * Boot the database service.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     *
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        return Connection::boot($app)->connect();
    }
}
