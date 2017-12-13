<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Crypt\Crypter;
use Core\Bootstrapers\Application;

class CrypterService extends Service
{
    /**
     * Service identifier name
     *
     * @var string
     */
    public static $name = 'crypter';

    /**
     * Boot the aplication service
     *
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    public static function boot(Application $app)
    {
        return Crypter::boot($app->encryption()->cipher);
    }
}
