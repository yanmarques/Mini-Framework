<?php

namespace Core\Database\Migrations;

use Phinx\Db\Adapter\AdapterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Phinx\Migration\AbstractMigration;

abstract class Migration extends AbstractMigration
{
    /**
     * @var float
     */
    protected $version;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * Table name
     * 
     * @var string
     */
    protected $name;

    /**
     * Table instance
     * 
     * @var Core\Database\Migrations\Table
     */
    protected $table;

    /**
     * Table options
     * 
     * @var array
     */
    protected $options = [];

    /**
     * Whether this migration is being applied or reverted
     *
     * @var bool
     */
    protected $isMigratingUp = true;

    /**
     * Class Constructor.
     *
     * @param int $version Migration Version
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     */
    public function __construct($version, InputInterface $input = null, OutputInterface $output = null)
    {
        parent::__construct($version, $input, $output);
    }

    /**
     * {@inheritdoc}
     */
    public function table($tableName, $options = array())
    {
        return new Table($tableName, $options, $this->getAdapter());
    }
}