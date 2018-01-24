<?php

namespace Core\Console;

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
use Core\Bootstrapers\HandleException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication implements ApplicationInterface
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
     * Observers configuration
     * 
     * @var Core\Stack\Stack
     */
    private $observers;

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
        parent::__construct('Mini Framework', null);
        $this->baseDir = $baseDir;

        // Before application boot
        $this->booting();
        
        // Boot aplication
        $this->boot();
        
        // // Run callbacks
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
        $status = parent::run($input, $secondary);
        dd($status);
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
        $this->fileHandler->include('core/Console/helpers.php');

        foreach($this->commands() as $command) {
            $this->add(new $command($this));
        }
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
        $this->exceptionReporter = Reporter::boot($this)->setOnConsole();
        
        // Php exception handler
        // Use exception reporter 
        HandleException::boot($this); 

        // Get encryption configuration from file
        $this->encryption = stack($this->fileHandler->getRequiredContent(
            $this->encryptionConfigPath()
        ));

        // Global observers
        $this->observers = stack($this->fileHandler->getRequiredContent(
            $this->observerConfigPath()
        ));

        // Get database configuration from file
        $this->database = stack($this->fileHandler->getRequiredContent(
            $this->databaseConfigPath()
        ));
    
        // Initialize configurations services 
        stack($this->configurationServices())->each(function ($value) {
            $this->services->add(
                ServiceDispatcher::dispatch($this, $value)
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

    private function commands()
    {
        return [
            \Core\Console\Commands\ControllerCommand::class
        ];  
    }
}
