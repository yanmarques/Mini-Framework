<?php

namespace Core\Views;

use Core\Exceptions\Files\FileNotFoundException;
use Core\Http\Response;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Interfaces\Http\ResponseStatusInterface;

class View implements ResponseStatusInterface
{
    /**
     * View is booted.
     *
     * @var bool
     */
    protected static $booted = false;

    /**
     * Singleton instance of class.
     *
     * @var Core\Views\View
     */
    private static $instance;

    /**
     * ApplicationInterface.
     *
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    /**
     * View matcher.
     *
     * @var Core\Views\ViewMatcher
     */
    private $matcher;

    /**
     * View absolute path.
     *
     * @var string
     */
    private $path;

    /**
     * Use a custom base path to views.
     *
     * @var string
     */
    private $customBasePath;

    /**
     * View params.
     *
     * @var array
     */
    protected $params = [];

    /**
     * View response status.
     *
     * @var int
     */
    protected $status = 200;

    /**
     * Constructor of class.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     *
     * @return Core\Views\View
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
        $this->matcher = new ViewMatcher($app);
    }

    /**
     * Boot singleton class.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     *
     * @return Core\Views\View
     */
    public static function boot(ApplicationInterface $app)
    {
        if (!self::$booted) {
            self::$instance = new self($app);
        }

        return self::$instance;
    }

    /**
     * Make view path.
     *
     * @param string $path       Realtive path to view
     * @param string $customPath Custom path to view
     *
     * @return void
     */
    public static function make(string $path, string $customPath = null)
    {
        $instance = static::$instance;
        $absolutePath = $instance->matcher->get($path, $customPath);

        // View was not found
        if (!$instance->app->fileHandler()->isFile($absolutePath)) {
            throw new FileNotFoundException("View [{$absolutePath}] was not found.");
        }

        $instance->path = $absolutePath;

        return $instance;
    }

    /**
     * Parameters to render view with.
     *
     * @param string $name  Parameter name
     * @param mixed  $value Parameter value
     *
     * @return Core\Views\View
     */
    public function with(array $params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Get view response status code.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set response Http status.
     *
     * @param int $status Http success status
     *
     * @return Core\Views\View
     */
    public function status(int $status)
    {
        if (!Response::isSuccessfull($status)) {
            throw new \RuntimeException("Invalid status [$status] for successfull response.");
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Include view file to script with parameters.
     *
     * @return void
     */
    public function render()
    {
        foreach ($this->params as $key => $value) {
            ${$key} = $value;
        }

        include $this->path;
    }

    /**
     * Get view path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }
}
