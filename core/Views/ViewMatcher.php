<?php

namespace Core\Views;

use Core\Interfaces\Bootstrapers\ApplicationInterface;

class ViewMatcher
{
    /**
     * ApplicationInterface
     *
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    /**
     * Constructor of class
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return Core\Views\ViewMatcher
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Match a given path to view and return path
     *
     * @param string $path Path to view
     * @return string|false
     */
    public function get(string $path, string $customPath = null)
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $path) . '.php';
        return ($customPath ?: $this->app->viewsDir()) . $path;
    }
}
