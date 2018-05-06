<?php

namespace Core\Console;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $name;

    /**
     * Command description.
     *
     * @var string
     */
    protected $description;

    /**
     * The input interface implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The output interface implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    /**
     * Class constructor.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @param string                                            $name Command name
     */
    public function __construct(ApplicationInterface $app)
    {
        parent::__construct($this->name);
        $this->setApplication($app);
        $this->setDescription($this->description);
        $this->addArguments();
        $this->addOptions();
    }

    /**
     * Get default stub path.
     *
     * @return string
     */
    public function stubPath()
    {
        return $this->getApplication()->coreDir().implode(DIRECTORY_SEPARATOR, ['Console', 'Commands', 'stubs']).DIRECTORY_SEPARATOR;
    }

    /**
     * Write a string as information output.
     *
     * @param string          $string
     * @param null|int|string $verbosity
     *
     * @return void
     */
    public function info($string)
    {
        $this->line($string, 'info');
    }

    /**
     * Write a string as standard output.
     *
     * @param string          $string
     * @param string          $style
     * @param null|int|string $verbosity
     *
     * @return void
     */
    public function line($string, $style = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled);
    }

    /**
     * Write a string as comment output.
     *
     * @param string          $string
     * @param null|int|string $verbosity
     *
     * @return void
     */
    public function comment($string)
    {
        $this->line($string, 'comment');
    }

    /**
     * Write a string as question output.
     *
     * @param string          $string
     * @param null|int|string $verbosity
     *
     * @return void
     */
    public function question($string)
    {
        $this->line($string, 'question');
    }

    /**
     * Write a string as error output.
     *
     * @param string          $string
     * @param null|int|string $verbosity
     *
     * @return void
     */
    public function error($string)
    {
        $this->line($string, 'error');
    }

    /**
     * Write a string in an alert box.
     *
     * @param string $string
     *
     * @return void
     */
    public function alert($string)
    {
        $this->error(str_repeat('*', strlen($string) + 12));
        $this->error('*     '.$string.'     *');
        $this->error(str_repeat('*', strlen($string) + 12));

        $this->output->writeln('');
    }

    /**
     * Parse a path argument and return a StdClass with class name, file path and custom path.
     *
     * @param string $name Argument name
     * @param array  $path Path to file
     *
     * @return \StdClass
     */
    public function parsePathArgument(string $name, $path = [])
    {
        // Argument stack
        $argument = stack(explode(DIRECTORY_SEPARATOR, $this->input->getArgument($name)));

        // Add argument path to default path
        $argument->each(function ($customPath) use (&$path) {
            $path[] = $customPath;
        });

        $class = $argument->pop();
        $path[count($path) - 1] .= '.php';

        // Create and StdClass to pack path data
        $pathClass = new \StdClass();

        // Class name
        $pathClass->class = $class;

        // Absolute path to create file
        // No really directory verification is made, so when creating file, use recursive mode to create left directories
        $pathClass->path = DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $path);

        // Custom path on user argument
        $pathClass->customPath = $argument->all();

        return $pathClass;
    }

    /**
     * Handle console commands input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    abstract public function handle(InputInterface $input, OutputInterface $output);

    /**
     * Execute the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        return $this->handle($input, $output);
    }

    /**
     * Get command arguments as an array.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get command options as an array.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * Add command arguments.
     *
     * @return void
     */
    private function addArguments()
    {
        foreach ($this->getArguments() as $arguments) {
            $this->addArgument(...$arguments);
        }
    }

    /**
     * Add command options.
     *
     * @return void
     */
    private function addOptions()
    {
        foreach ($this->getOptions() as $options) {
            $this->addOption(...$options);
        }
    }
}
