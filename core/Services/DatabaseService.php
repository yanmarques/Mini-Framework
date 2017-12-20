<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Bootstrapers\Application;
use Core\Database\Connection;

class DatabaseService extends Service
{
     /**
     * Service identifier name
     *
     * @var string
     */
    public static $name = 'database';

    /**
     * Boot the database service
     *
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    public static function boot(Application $app)
    {
        return Connection::boot($app);
    }
}
