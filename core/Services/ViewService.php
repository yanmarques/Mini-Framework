<?php

namespace Core\Services;

use Core\Services\Abstracts\Service;
use Core\Views\View;
use Core\Bootstrapers\Application;

class ViewService extends Service
{
    /**
     * Service identifier name
     *
     * @var string
     */
    public static $name = 'view';

    /**
     * Boot the aplication service
     *
     * @param Core\Bootstrapers\Application $app
     * @return mixed
     */
    public static function boot(Application $app)
    {
        return View::boot($app);
    }
}
