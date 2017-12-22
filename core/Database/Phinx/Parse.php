<?php

namespace Core\Database\Phinx;

use Core\Support\Creator;
use Core\Interfaces\Bootstrapers\ApplicationInterface;

class Parse
{
    /**
     * ApplicationInterface
     * 
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    /**
     * Path to phinx stub
     * 
     * @var string
     */
    private $phinxStub = __DIR__ .DIRECTORY_SEPARATOR. 'stubs' .DIRECTORY_SEPARATOR. 'phinx.yml.stub'; 

    public function __construct(ApplicationInterface $app)
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
            'DummyPathMigrations'     => '%%PHINX_CONFIG_DIR%%'.DIRECTORY_SEPARATOR.$config->paths->get('migrations'),
            'DummyPathSeeds'          => '%%PHINX_CONFIG_DIR%%'.DIRECTORY_SEPARATOR.$config->paths->get('seeds'),
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