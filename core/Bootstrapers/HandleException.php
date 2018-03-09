<?php

namespace Core\Bootstrapers;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Http\RedirectResponse;
use Core\Http\Response;
use Core\Reflector\Reflector;

class HandleException
{   
    /**
     * ApplicationInterface
     * 
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    protected $app;

    /**
     * Catcher handler instance.
     * 
     * @var App\Exceptions\Catcher
     */
    protected $catcher;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;

        if ( ! class_exists("\\App\\Exceptions\\Catcher") ) {
            throw new \Core\Exceptions\Files\FileNotFoundException("Class [\\App\\Exceptions\\Catcher] was not found.");
        }

        $this->catcher = new \App\Exceptions\Catcher;

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
        return new static($app);
    }

    /**
     * Handle uncaught exception
     * 
     * @param mixed $e Exception
     */
    public function handleException($e)
    {
        $response = $this->getCatcherException($e);

        // Handle user catcher response.
        if ( ! is_null($response) ) {
            return $this->buildResponse($response);
        }

        $this->app->reporter()->report($e, $this->catcher);
    }

    /**
     * Handle php errors
     * 
     * @param mixed $e Errors
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        $response = $this->getCatcherError($errno, $errstr, $errfile, $errline);

        // Handle user catcher response.
        if ( ! is_null($response) ) {
            return $this->buildResponse($response);
        }

        if ( $this->app->services()->config->env == 'dev' ) {

            // Let Whoops render a pretty explained response about the error.
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            return $whoops->handleError($errno, $errstr, $errfile, $errline);
        }
    }

    /**
     * Get catcher class on app directory to verify user error handler.
     * 
     * @return mixed|null
     */
    protected function getCatcherError($errno, $errstr, $errfile, $errline)
    {
        if ( class_exists("\\App\\Exceptions\\Catcher") ) {
            return Reflector::bind($this->catcher)->callMethod('onError', [$errno, $errstr, $errfile, $errline]);
        }

        return null;
    }

    /**
     * Get catcher class on app directory to verify user exception handler.
     * 
     * @return mixed|null
     */
    protected function getCatcherException($exception)
    {
        if ( class_exists("\\App\\Exceptions\\Catcher") ) {
            return Reflector::bind($this->catcher)->callMethod('onException', [$exception]);
        }

        return null;
    }

    /**
     * Build the response to send.
     * 
     * @return Core\Http\Response
     */
    protected function buildResponse($response)
    {
        // Redirect browser due to user catcher response.
        if ( $response instanceof RedirectResponse ) {
            return (new Response($response))->send();
        }

        // Return a Http response to browser with internal server error.
        if ( is_string($response) ) {
            return (new Response($response, 500))->send();
        }

        // Return a JSON format response.
        if ( is_array($response) ) {
            return (new Response(json_encode($response), 500))->send();
        }
    }
}