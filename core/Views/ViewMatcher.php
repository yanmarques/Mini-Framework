<?php

namespace Core\Views;

use Core\Bootstrapers\Application;

class ViewMatcher
{
    /**
     * Application
     *
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * Constructor of class
     *
     * @param Core\Bootstrapers\Application $app
     * @return Core\Views\ViewMatcher
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Match a given path to view and return path
     *
     * @param string $path Path to view
     * @return string|false
     */
    public function get(string $path)
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $path) . '.php';
        return $this->app->viewsDir() . $path;
    }
}
