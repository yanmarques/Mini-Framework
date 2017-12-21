<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Bootstrapers\Application;
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
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    public static function boot(Application $app)
    {
        $phinx = new Parse($app);
        $phinx->create();
        return $phinx;
    }
}
