<?php

namespace Core\Console\Commands;

use Core\Console\Command;
use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $name = 'migrate';

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
        // Use phinx to migrate
        $phinx = new PhinxApplication();
        $phinx->doRun($input, $output);
    }
}
