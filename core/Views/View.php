<?php

namespace Core\Views;

use Core\Bootstrapers\Application;
use Core\Exceptions\Files\FileNotFoundException;

class View
{
    /**
     * View is booted
     *
     * @var bool
     */
    protected static $booted = false;

    /**
     * Singleton instance of class
     *
     * @var Core\Views\View
     */
    private static $instance;

    /**
     * Application
     *
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * View matcher
     *
     * @var Core\Views\ViewMatcher
     */
    private $matcher;

    /**
     * View absolute path
     * 
     * @var string
     */
    private $path;

    /**
     * Constructor of class
     *
     * @param Core\Bootstrapers\Application $app
     * @return Core\Views\View
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->matcher = new ViewMatcher($app);
    }

    /**
     * Boot singleton class
     *
     * @param Core\Bootstrapers\Application $app
     * @return Core\Views\View
     */
    public static function boot(Application $app)
    {
        if ( ! self::$booted ) {
            self::$instance = new self($app);
        }

        return self::$instance;
    }

    /**
     * Make view path
     * 
     * @param string $path Realtive path to view
     * @return void
     */
    static function make(string $path)
    {
        $instance = static::$instance;
        $absolutePath = $instance->matcher->get($path);
        
        // View was not found
        if ( ! $instance->app->fileHandler()->isFile($absolutePath) ) {
            throw new FileNotFoundException("View [{$absolutePath}] was not found.");
        }

        $instance->path = $absolutePath;
        return $instance;
    }

    /**
     * Get view path
     * 
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Return rendered view
     *
     * @return string
     */
    public function render()
    {
        return $this->app->fileHandler()->read($this->path);
    }
}
