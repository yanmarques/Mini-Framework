<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Database\Phinx\Parse;

class PhinxService extends Service
{
     /**
     * Service identifier name
     *
     * @var string
     */
    public static $name = 'phinx';

    /**
     * Boot the aplication service
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        $phinx = new Parse($app);
        $phinx->create();
        return $phinx;
    }
}
