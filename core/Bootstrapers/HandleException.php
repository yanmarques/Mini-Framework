<?php

namespace Core\Bootstrapers;

class HandleException
{   
    /**
     * Application
     * 
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * Boot handler
     * 
     * @param Core\Bootstrapers\Application $app
     * @return void
     */
    public function boot(Application $app)
    {
        $this->app = $app;

        \error_reporting(-1);

        set_error_handler([$this, "handleError"]);

        \set_exception_handler([$this, "handleException"]);
    }

    /**
     * Handle uncaught exception
     * 
     * @param mixed $e Exception
     */
    public function handleException($e)
    {
        $this->app->reporter()->report($e);
    }

    /**
     * Handle php errors
     * 
     * @param mixed $e Errors
     */
    public function handleError($e)
    {
        $this->app->reporter()->report($e);
    }
}