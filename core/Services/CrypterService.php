<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Crypt\Crypter;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

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
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        return Crypter::boot($app->encryption()->cipher);
    }
}
