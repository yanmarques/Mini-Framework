<?php

namespace Core\Bootstrapers;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Services\Stack\ServicesStack;
use Core\Files\FileHandler;
use Core\Http\Request;
use Core\Http\RedirectResponse;
use Core\Http\Response;
use Core\Views\View;

class Application implements ApplicationInterface
{

    /**
     * Application instance
     *
     * @var Core\Bootstrapers\Application
     */
    private static $instance;

    /**
     * Base application directory
     *
     * @var string
     */
    private $baseDir;

    /**
     * Path to services configuration
     *
     * @var string
     */
    private $servicesConfig = DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'services.php';

     /**
     * Path to encryption configuration
     *
     * @var string
     */
    private $encryptionConfig = DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'encryption.php';

    /**
     * Services collection
     *
     * @var Core\Services\ServiceCollection
     */
    private $services;

    /**
     * Encryption collection
     *
     * @var array
     */
    private $encryption;

    /**
     * Handle file actions
     *
     * @var Core\Files\FileHandler
     */
    private $fileHandler;

     /**
     * Constructor of class
     *
     * @param string $baseDir Application directory
     * @return Core\Bootstrapers\Application
     */
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;

        $this->bootConfig();
        $this->bootEncryption();
        $this->bootServices();

        static::$instance = $this;

        $this->bootAppConfiguration();
    }

    /**
     * Get an instance of application
     *
     * @return Core\Bootstrapers\Application
     */
    static function instance()
    {
        return self::$instance;
    }

    /**
     * Return services collection class
     *
     * @return Core\Stack\Stack
     */
    public function services()
    {
        return $this->services;
    }

    /**
     * Return encryption collection class
     *
     * @return Core\Stack\Stack
     */
    public function encryption()
    {
        return $this->encryption;
    }

    /**
     * Return file handler class
     *
     * @return Core\Files\FileHandler
     */
    public function fileHandler()
    {
        return $this->fileHandler;
    }

    /**
     * Return application base directory
     *
     * @return string
     */
    public function baseDir()
    {
        return $this->baseDir;
    }

    /**
     * Return application app directory
     *
     * @return string
     */
    public function appDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'app';
    }

    /**
     * Return application core directory
     *
     * @return string
     */
    public function coreDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'core';
    }

    /**
     * Return application views directory
     *
     * @return string
     */
    public function viewsDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'views';
    }

     /**
     * Initialize all services from configuration
     *
     * @return void
     */
    private function bootServices()
    {
        $services = stack($this->requireServices());

        $services = $services->map(function ($service) {
            return ServiceDispatcher::dispatch($this, $service);
        })->collapse();

        $this->services = new ServicesStack($services);
    }

    private function bootEncryption()
    {
        $this->encryption = stack($this->requireEncryption());
    }

    /**
     * Initialize configuration classes
     *
     * @param Core\Bootstrapers\Application
     */
    protected function bootConfig()
    {
        $this->fileHandler = new FileHandler($this);
        $this->fileHandler->include('core/Support/helperFunctions.php');
    }

    protected function bootAppConfiguration()
    {
        $this->fileHandler->include('core/Support/configFunctions.php');
        $this->services()->config()->resolveConfiguration();
    }

    /**
     * Get array with services from services configuration
     *
     * @return array
     */
    private function requireServices()
    {
        return $this->fileHandler->getRequiredContent(
            $this->baseDir .DIRECTORY_SEPARATOR. $this->servicesConfig
        );
    }

    /**
     * Get array with encryption options from configuration
     *
     * @return array
     */
    private function requireEncryption()
    {
        return $this->fileHandler->getRequiredContent(
            $this->baseDir .DIRECTORY_SEPARATOR. $this->encryptionConfig
        );
    }

    /**
     * Handle an incomming request
     *
     * @param Core\Http\Request $request Incomming request
     * @return Core\Http\Response
     */
    public function handle(Request $request)
    {
        $response = $this->services()->routing()->dispatch($request);
        
        if ( $response instanceof RedirectResponse ) {
            return new Response('', $response->getStatus(), $response->getHeaders());
        } 
        
        if ( $response instanceof View ) {
            return new Response($response->render());
        }

        return new Response((string) $response);
    }
}
