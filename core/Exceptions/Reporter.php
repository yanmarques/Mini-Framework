<?php

namespace Core\Exceptions;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Http\Response;
use Core\Views\View;
use Symfony\Component\Console\Output\ConsoleOutput;

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
     * ApplicationInterface
     * 
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    /**
     * Application is running on console mode
     * 
     * @var bool
     */
    private $console = false;

    /**
     * Constructor of class
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return Core\Exceptions\Reporter
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
        $this->exceptions = stack();
    }

    /**
     * Boot Handler singleton
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @return Core\Exceptions\Reporter
     */
    static function boot(ApplicationInterface $app)
    {
        if ( ! static::$booted ) {
            static::$instance = new self($app);
        }

        return static::$instance;
    }

    /**
     * Set reporter to console mode
     * 
     * @return Core\Exceptions\Reporter
     */
    public function setOnConsole()
    {
        $this->console = true;
        return $this;
    }

    /**
     * Report exception
     * 
     * @param mixed $e Exception to report
     */
    public function report($e)
    {
        if ( $this->console ) {
            return $this->app->renderException($e, new ConsoleOutput);
        }

        if ( ! $this->shouldReport() ) {
            return $this->response($e)->send();
        }
        
        if ( $this->isHttp($e) ) {
            return (new Response($this->buildView($e), $e->getStatus()))->send();
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
     * Verify wheter exception is Http Exception
     * 
     * @param mixed $e Exception
     * @return bool
     */
    private function isHttp($e)
    {
        return is_object($e) && ( $e instanceof HttpResponseException || 
            get_parent_class($e) == 'Core\Exceptions\Http\HttpResponseException' );
    }

    /**
     * Send exception response in case of production environment
     * 
     * @return Core\Http\Response
     */
    private function response($e)
    {
        if ( $this->isHttp($e) ) {
            $view = $this->buildView($e);
            return new Response($view, $e->getStatus());
        }

        return new Response(\file_get_contents($this->viewsPath() . '500.php'), 500);
    }

    /**
     * Build http response view
     * 
     * @param mixed $e Exception
     * @return Core\Views\View
     */
    private function buildView($e)
    {
        $status = (string) in_array($e->getStatus(), [404, 415, 500, 503, 429]) ? $e->getStatus() : 500;
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