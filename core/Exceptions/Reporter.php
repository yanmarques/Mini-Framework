<?php

namespace Core\Exceptions;

use Core\Bootstrapers\Application;
use Core\Http\Response;
use Core\Views\View;

class Reporter
{
    /**
     * Handler is booted
     * 
     * @var bool
     */
    private static $booted;

    /**
     * Singleton instance
     * 
     * @var Core\Exceptions\Reporter
     */
    private static $instance;

    /**
     * Stack with exceptions
     * 
     * @var Core\Stack\Stack
     */
    private $exceptions;

    /**
     * Application
     * 
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * Constructor of class
     * 
     * @param Core\Bootstrapers\Application
     * @return Core\Exceptions\Reporter
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->exceptions = stack();
    }

    /**
     * Boot Handler singleton
     * 
     * @param Core\Bootstrapers\Application
     * @return Core\Exceptions\Reporter
     */
    static function boot(Application $app)
    {
        if ( ! static::$booted ) {
            static::$instance = new self($app);
        }

        return static::$instance;
    }

    /**
     * Report exception
     * 
     * @param mixed $e Exception to report
     */
    public function report($e)
    {
        if ( ! $this->shouldReport() ) {
            return $this->response($e)->send();
        }

        return $this->render($e);
    } 

    /**
     * Verify wheter application should report exception
     * 
     * @return bool
     */
    private function shouldReport()
    {
        return $this->app->services()->config()->env == 'dev';
    }

    /**
     * Send exception response in case of production environment
     * 
     * @return Core\Http\Response
     */
    private function response($e)
    {
        if ( $e instanceof HttpResponseException ) {
            $view = $this->buildView($e);
            return new Response($view, $e->getStatus());
        }

        return new Response(View::make('500', $this->viewsPath()));
    }

    /**
     * Build http response view
     * 
     * @param mixed $e Exception
     * @return Core\Views\View
     */
    private function buildView($e)
    {
        $status = in_array($e->getStatus(), [404, 500, 503, 429]) ? $e->getStatus() : 500;
        return View::make($status, $this->viewsPath());
    }

    /**
     * Path to views
     * 
     * @return string
     */
    private function viewsPath()
    {
        return $this->app->coreDir() . 'Exceptions' .DIRECTORY_SEPARATOR. 'views' .DIRECTORY_SEPARATOR;
    }

    /**
     * Render uncaught exception
     * 
     * @throws Core\Exceptions\Exception
     * 
     * @param mixed $e Exception
     * @return void
     */
    private function render($e)
    {
        throw new Exception($e);
    }
}