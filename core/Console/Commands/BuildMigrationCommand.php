<?php

namespace Core\Console\Commands;

use Core\Console\Command;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Support\Creator;
use Phinx\Util\Util;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildMigrationCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $name = 'build:migration';

    /**
     * Stub name.
     *
     * @var string
     */
    protected $stub = 'migration.stub';

    /**
     * Command description.
     *
     * @var string
     */
    protected $description = 'Build an migration class.';

    /**
     * Class constructor.
     *
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app
     * @param string                                            $name Command name
     */
    public function __construct(ApplicationInterface $app)
    {
        parent::__construct($app);
    }

    /**
     * Handle console commands input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    public function handle(InputInterface $input, OutputInterface $output)
    {
        // Argument stack
        $argument = stack(explode(DIRECTORY_SEPARATOR, $this->input->getArgument('name')));

        // Directory to file
        $path = ['database', 'migrations'];

        // Add argument path to default path
        $argument->each(function ($customPath) use (&$path) {
            $path[] = $customPath;
        });

        $class = stack(explode('_', $argument->pop()));
        $class = $class->implode('', null, function ($item) {
            return ucfirst($item);
        });

        $path[count($path) - 1] = (string) Util::getCurrentTimestamp().'_'.$path[count($path) - 1].'.php';

        // Path to create file
        $path = $this->getApplication()->baseDir().DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $path);

        // Verify wheter file already exists
        if ($this->getApplication()->fileHandler()->isFile($path)) {
            throw new \RuntimeException("File [$path] already exists");
        }

        $table = $input->getOption('table');

        // Stub path
        $stub = $this->stubPath();

        // Parse table configuration
        if ($table == null) {
            $stub .= 'migration_blank.stub';
        } else {
            $stub .= $this->stub;
        }

        // Use creator singleton to create a file from stubs configuration
        Creator::parse($path, $stub, $this->dummies($class, $table));

        $this->info('Migration created successfully.');

        // Dispatch autoload event
        observe('autoload');
    }

    /**
     * Command arguments.
     *
     * @var array
     */
    protected function getArguments()
    {
        return [
            [
                'name', InputArgument::REQUIRED, 'Controller name',
            ],
        ];
    }

    /**
     * Command options.
     *
     * @var array
     */
    protected function getOptions()
    {
        return [
            [
                'table', null, InputOption::VALUE_REQUIRED, 'Table name',
            ],
        ];
    }

    /**
     * Get command dummies.
     *
     * @param string $class
     *
     * @return array
     */
    private function dummies(string $class, $table)
    {
        if ($table == null) {
            return [
                'DummyClass' => $class,
            ];
        }

        return [
            'DummyClass' => $class,
            'DummyTable' => "'".$table."'",
        ];
    }
}
