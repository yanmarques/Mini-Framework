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
     * Use a custom base path to views
     * 
     * @var string
     */
    private $customBasePath;

    /**
     * View params
     * 
     * @var array
     */
    protected $params = [];

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
     * @param string $customPath Custom path to view
     * @return void
     */
    static function make(string $path, string $customPath = null)
    {
        $instance = static::$instance;
        $absolutePath = $instance->matcher->get($path, $customPath);
        
        // View was not found
        if ( ! $instance->app->fileHandler()->isFile($absolutePath) ) {
            throw new FileNotFoundException("View [{$absolutePath}] was not found.");
        }

        $instance->path = $absolutePath;
        return $instance;
    }

    /**
     * Parameters to render view with
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value
     * @return Core\Views\View
     */
    public function with(array $params)
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Include view file to script with parameters
     * 
     * @return void
     */
    public function render()
    {
        foreach($this->params as $key => $value) {
            ${$key} = $value;
        }
        
        include $this->path;
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
}
