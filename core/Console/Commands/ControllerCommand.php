<?php

namespace Core\Console\Commands;

use Core\Console\Command;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Core\Support\Creator;

class ControllerCommand extends Command
{
    /**
     * Controller namespace
     * 
     * @var string
     */
    private $namespace = 'App\Http\Controllers';

    /**
     * Command name
     * 
     * @var string
     */
    protected $name = 'build.controller';

    /**
     * Stub name
     * 
     * @var string
     */
    protected $stub = 'controller.stub';

    /**
     * Command arguments
     * 
     * @var array
     */
    protected $arguments = [
        [
            'name', InputArgument::REQUIRED, 'Controller name'
        ]
    ];

    /**
     * Command description
     * 
     * @var string
     */
    protected $description = 'Build an controller class.';

    /**
     * Class constructor
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @param string $name Command name
     */
    public function __construct(ApplicationInterface $app)
    {
        parent::__construct($app);
    }

    /**
     * Handle console commands input
     * 
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return mixed
     */
    public function handle(InputInterface $input, OutputInterface $output)
    {
        // Class name
        $argument = $this->parsePathArgument('name', ['Http', 'Controllers']);

        // Stub path
        $stub =  $this->stubPath() . $this->stub;

        // Path to create file
        $path = $this->getApplication()->appDir() . $argument->path;
        
        // Verify wheter file already exists
        if ( $this->getApplication()->fileHandler()->isFile($path) ) {
            throw new \RuntimeException("File [$path] already exists");
        }
        
        // Use creator singleton to create a file from stubs configuration
        Creator::parse($path, $stub, $this->dummies($argument->class, $argument->customPath));

        $this->info('Controller created successfully.');

        // Dispatch autoload event
        observe('autoload');
    }

    /**
     * Get command dummies
     * 
     * @param string $class 
     * @return array
     */
    private function dummies(string $class, array $args=[])
    {
        $namespace = empty($args) ? $this->namespace : $this->namespace .'\\'. implode('\\', $args);
        return [
            'DummyNamespace' => $namespace,
            'DummyClass' => $class
        ];
    }
}