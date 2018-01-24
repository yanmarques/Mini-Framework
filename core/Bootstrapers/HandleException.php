<?php

namespace Core\Bootstrapers;

use Core\Interfaces\Bootstrapers\ApplicationInterface;

class HandleException
{   
    /**
     * ApplicationInterface
     * 
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;

        \error_reporting(-1);

        \set_error_handler([$this, "handleError"]);

        \set_exception_handler([$this, "handleException"]);
    }

    /**
     * Boot handler
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return void
     */
    static function boot(ApplicationInterface $app)
    {
        return new self($app);
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
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        echo "Error: {$errstr}. File: {$errfile}. Line: {$errline}\n";die;
    }
}