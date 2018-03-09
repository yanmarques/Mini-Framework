<?php

namespace Core\Bootstrapers;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Services\Stack\ServicesStack;
use Core\Services\Dispatcher\ServiceDispatcher;
use Core\Files\FileHandler;
use Core\Files\BasePath;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\RedirectResponse;
use Core\Views\View;
use Core\Exceptions\Reporter;
use Core\Reflector\Reflector;
use Core\Support\Creator;

class Application implements ApplicationInterface
{
    use BasePath;

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
    private $encryption = [];

    /**
     * Middleware stack
     *
     * @var array
     */
    private $middleware = [];

    /**
     * Database configuration
     * 
     * @var array
     */
    private $database = [];

    /**
     * Observers configuration.
     * 
     * @var array
     */
    private $observers = [];

    /**
     * Session configuration.
     * 
     * @var array
     */
    private $session = [];

    /**
     * Repository with class instances.
     * 
     * @var Core\Foundation\Repository
     */
    private $repository;

    /**
     * Handle file actions.
     *
     * @var Core\Files\FileHandler
     */
    private $fileHandler;

     /**
     * Exception reporter class.
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
     * Return observers configuration
     * 
     * @return Core\Stack\Stack
     */
    public function observers()
    {
        return $this->observers;
    }

    /**
     * Return session configuration
     * 
     * @return Core\Stack\Stack
     */
    public function session()
    {
        return $this->session;
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
        $this->services = new ServicesStack;
        $this->addRegister($this->services);

        $this->fileHandler = FileHandler::boot($this->baseDir());
        $this->addRegister($this->fileHandler);

        // Include some helper functions to boot system
        $this->fileHandler->include('core/Support/baseFunctions.php');

        dd($this->resolve(\App\Exceptions\Catcher::class));

        $this->updateInstance();
    }

    /**
     * Make an instance of base name classe trying to resolve object with already registered
     * instances.
     * 
     * @param string $name Class name
     * @param array $dependencies Class dependencies
     * @return mixed|null
     */
    public function resolve(string $name, array $dependencies = [])
    {
        // We assume here that the class looked is already instantiated.
        if ( isset($this->container[$name]) ) {
            return $this->container[$name];
        }

        // Try to resolve dependecies from container.
        foreach($dependencies as $key => $dependencie) {
            $dependencies[$key] = $this->resolve($name);
        }
        
        // And we assume here that any class was instantiated yet. We will use reflector
        // tool resolve class instance.
        return Reflector::bind($name)->depends($dependencies)
            ->withContainers($this->container)
            ->getObject();
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
        $this->updateInstance();

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
        Reflector::boot($this);
        Creator::boot($this->baseDir());
        FileHandler::boot($this->baseDir());
    }

    /**
     * Update application instance
     * 
     * @return void
     */
    private function updateInstance()
    {
        static::$instance = $this;
        $this->repository->bind(static::class, $this);
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

        // Global observers
        $this->observers = stack($this->fileHandler->getRequiredContent(
            $this->observerConfigPath()
        ));

        // Get middleware configuration from file
        $this->middleware = stack($this->fileHandler->getRequiredContent(
            $this->middlewareConfigPath()
        ));

        // Get session configuration from file
        $this->session = stack($this->fileHandler->getRequiredContent(
            $this->sessionConfigPath()
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
     * Get configuration services to run before application boots
     * Configuration services are needed for application to boots
     * 
     * @return array
     */
    private function configurationServices()
    {
        return [
            \Core\Services\CrypterService::class,
            \Core\Services\ConfigService::class,
            \Core\Services\DatabaseService::class,
            \Core\Services\ObserverService::class
        ];
    }

    /**
     * Add a registered class to container stack.
     * 
     * @param mixed $registered Object registered
     * @return void
     */
    private function addRegister($registered)
    {
        $this->container[get_class($registered)] = $registered;
    }
}
