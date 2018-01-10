<?php

namespace Core\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

class ControllerCommand extends Command
{
    /**
     * Command name
     * 
     * @var string
     */
    private $name = 'build.controller';

    /**
     * Symfony InputInterface
     * 
     * @var Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * Symfony OuputInterface
     * 
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * Class constructor
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @param string $name Command name
     */
    public function __construct(ApplicationInterface $app)
    {
        parent::__construct($this->name);
        $this->setApplication($app);
        $this->addArguments();
        $this->setDescription('Build a Controller class.');
    }

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->run($this->input = $input, $this->output = $output);
    }

    /**
     * Add command arguments
     * 
     * @return void
     */
    private function addArguments()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Controller name');
    }

    /**
     * Run the the command function.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return mixed
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArguments('name');
        observe('autoload');
    }
}