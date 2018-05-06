<?php

namespace Core\Services;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Services\Abstracts\Service;
use Core\Views\View;

class ViewService extends Service
{
    /**
     * Service identifier name.
     *
     * @var string
     */
    public static $name = 'view';

    /**
     * Boot the aplication service.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     *
     * @return mixed
     */
    public static function boot(ApplicationInterface $app)
    {
        return View::boot($app);
    }
}
