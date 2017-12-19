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
     * Services stack
     *
     * @var Core\Services\ServiceStack
     */
    private $services;

    /**
     * Encryption stack
     *
     * @var array
     */
    private $encryption;

    /**
     * MIddleware stack
     *
     * @var Core\Stack\Stack
     */
    private $middleware;

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
        $this->fileHandler = new FileHandler($this);
        $this->services = new ServicesStack;

        // Include some helper functions to boot system
        $this->fileHandler->include('core/Support/baseFunctions.php');

        $this->bootEncryption();
        $this->bootEnvironment();
        $this->bootMiddleware();
        $this->bootServices();

        static::$instance = $this;

        // Include all application helper functions
        $this->fileHandler->include('core/Support/helpers.php');
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
     * Return services stack class
     *
     * @return Core\Stack\Stack
     */
    public function services()
    {
        return $this->services;
    }

    /**
     * Return encryption stack class
     *
     * @return Core\Stack\Stack
     */
    public function encryption()
    {
        return $this->encryption;
    }

    /**
     * Return middleware stack class
     *
     * @return Core\Stack\Stack
     */
    public function middleware()
    {
        return $this->middleware;
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
        return $this->baseDir . DIRECTORY_SEPARATOR;
    }

    /**
     * Return application app directory
     *
     * @return string
     */
    public function appDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'app' .DIRECTORY_SEPARATOR;
    }

    /**
     * Return application core directory
     *
     * @return string
     */
    public function coreDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'core' .DIRECTORY_SEPARATOR;
    }

    /**
     * Return application routes directory
     *
     * @return string
     */
    public function routesDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'routes' .DIRECTORY_SEPARATOR;
    }

    /**
     * Return application views directory
     *
     * @return string
     */
    public function viewsDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'views' .DIRECTORY_SEPARATOR;
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
        return new Response($response);
    }

    /**
     * Initialize all services from configuration
     *
     * @return void
     */
    private function bootServices()
    {
        $services = stack($this->fileHandler->getRequiredContent(
                            $this->servicesConfigPath()
                    ));

        $services = $services->map(function ($service) {
            return ServiceDispatcher::dispatch($this, $service);
        })->collapse();

        $this->services->add($services->all());
    }

    /**
     * Initialize encyption application service
     *
     * @return void
     */
    private function bootEncryption()
    {
        $this->encryption = stack($this->fileHandler->getRequiredContent(
            $this->encryptionConfigPath()
        ));

        $this->services->add(
            ServiceDispatcher::dispatch($this, \Core\Services\CrypterService::class),
            'crypter'
        );
    }

    /**
     * Initialize environment configuration
     *
     * @return void
     */
    protected function bootEnvironment()
    {
        $this->services->add(
            ServiceDispatcher::dispatch($this, \Core\Services\ConfigService::class),
            'config'
        );
    }

    /**
     * Initialize middleware configuration
     *
     * @return void
     */
    protected function bootMiddleware()
    {
        $this->middleware = stack($this->fileHandler->getRequiredContent(
                $this->middlewareConfigPath()
            ));
    }

    /**
     * Get path to services configuration
     *
     * @return string
     */
    private function servicesConfigPath()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'services.php';
    }

    /**
     * Get path to encryption configuration
     *
     * @return string
     */
    private function encryptionConfigPath()
    {
        return $this->baseDir . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'encryption.php';
    }

    /**
     * Get path to encryption configuration
     *
     * @return string
     */
    private function middlewareConfigPath()
    {
        return $this->baseDir . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'middleware.php';
    }
}
