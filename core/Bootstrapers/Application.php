<?php

namespace Core\Bootstrapers;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Services\Stack\ServicesStack;
use Core\Files\FileHandler;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\RedirectResponse;
use Core\Views\View;
use Core\Exceptions\Reporter;
use Core\Reflector\Reflector;
use Core\Support\Creator;

class Application implements ApplicationInterface
{
    /**
     * ApplicationInterface instance
     *
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
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
     * Database configuration
     * 
     * @var Core\Stack\Stack
     */
    private $database;

    /**
     * Handle file actions
     *
     * @var Core\Files\FileHandler
     */
    private $fileHandler;

     /**
     * Exception reporter
     *
     * @var Core\Exceptions\Reporter
     */
    private $exceptionReporter;

     /**
     * Constructor of class
     *
     * @param string $baseDir ApplicationInterface directory
     * @return Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;

        // Before application boot
        $this->booting();

        // Boot aplication
        $this->boot();

        // Run callbacks
        $this->booted();
    }

    /**
     * Get an instance of application
     *
     * @return Core\Interfaces\Bootstrapers\ApplicationInterface
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
     * Return exception reporter
     *
     * @return Core\Exceptions\Reporter
     */
    public function reporter()
    {
        return $this->exceptionReporter;
    }

    /**
     * Return database configuration
     *
     * @return Core\Stack\Stack
     */
    public function database()
    {
        return $this->database;
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
     * Handle an an $input argument
     *
     * @param mixed
     * @return mixed
     */
    public function handle($input, $secondary = null)
    {
        $response = $this->services()->routing()->dispatch($input);

        // Set response status
        if ( $response instanceof RedirectResponse || $response instanceof View ) {
            return new Response($response, $response->getStatus());
        }

        return new Response($response);
    }

    /**
     * Executed before application boot services
     * 
     * @return void
     */
    public function booting()
    {
        $this->fileHandler = new FileHandler($this);
        $this->services = new ServicesStack;

        // Include some helper functions to boot system
        $this->fileHandler->include('core/Support/baseFunctions.php');
    }

    /**
     * Boot application
     * 
     * @return void
     */
    public function boot()
    {
        $this->registerSingletons();
        $this->bootConfiguration();
        $this->bootServices();
    }

    /**
     * Executed after application has been booted
     * 
     * @return void
     */
    public function booted()
    {
        static::$instance = $this;

        // Include all application helper functions
        $this->fileHandler->include('core/Support/helpers.php');
    }

    /**
     * Register initial singleton classes
     * 
     * @return void
     */
    private function registerSingletons()
    {
        Reflector::boot($this->fileHandler);
        Creator::boot($this);
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
    private function bootConfiguration()
    {
        // Initialize exception report
        $this->exceptionReporter = Reporter::boot($this);

        // Php exception handler
        // Use exception reporter 
        HandleException::boot($this);

        // Get encryption configuration from file
        $this->encryption = stack($this->fileHandler->getRequiredContent(
            $this->encryptionConfigPath()
        ));

        // Get database configuration from file
        $this->database = stack($this->fileHandler->getRequiredContent(
            $this->databaseConfigPath()
        ));

        // Get middleware configuration from file
        $this->middleware = stack($this->fileHandler->getRequiredContent(
            $this->middlewareConfigPath()
        ));

        // Initialize configurations services 
        stack($this->configurationServices())->each(function ($value, $key) {
            $this->services->add(
                ServiceDispatcher::dispatch($this, $value),
                $key
            );
        });
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

     /**
     * Get path to encryption configuration
     *
     * @return string
     */
    private function databaseConfigPath()
    {
        return $this->baseDir . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'database.php';
    }

    private function configurationServices()
    {
        return [
            'crypter' => \Core\Services\CrypterService::class,
            'config' => \Core\Services\ConfigService::class,
            'database' => \Core\Services\DatabaseService::class
        ];
    }
}
