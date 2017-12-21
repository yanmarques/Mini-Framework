<?php

namespace Core\Database\Phinx;

use Core\Support\Creator;
use Core\Bootstrapers\Application;

class Parse
{
    /**
     * Application
     * 
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * Path to phinx stub
     * 
     * @var string
     */
    private $phinxStub = __DIR__ .DIRECTORY_SEPARATOR. 'stubs' .DIRECTORY_SEPARATOR. 'phinx.yml.stub'; 

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Create phinx migration configuration
     * 
     * @return void
     */
    public function create()
    {
        Creator::parse($this->app->baseDir().'phinx.yml', $this->phinxStub, $this->parseDatabaseConfiguration());
    }  

    /**
     * Create a stub configuration with database configuration from application
     * 
     * @return array
     */
    private function parseDatabaseConfiguration()
    {
        // Database configuration
        $config = $this->app->database();

        return [
            'DummyPathMigrations'     => $config->paths->get('migrations'),
            'DummyPathSeeds'          => $config->paths->get('seeds'),
            'DummyEnvDefaultDatabase' => $this->app->services()->config()->env == 'production' ? 'production' : 'development',
            'DummyDriver'             => $config->driver,
            'DummyHost'               => $config->host,
            'DummyDbname'             => $config->dbname,
            'DummyUser'               => $config->user,
            'DummyPassword'           => $config->password,
            'DummyPort'               => $config->port,
        ];  
    }
}