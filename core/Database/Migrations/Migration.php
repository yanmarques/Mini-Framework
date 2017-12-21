<?php

namespace Core\Database\Migrations;

use Phinx\Migration\MigrationInterface;
use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Migration implements MigrationInterface
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
    final public function __construct($version, InputInterface $input = null, OutputInterface $output = null)
    {
        $this->version = $version;
        
        if ( ! is_null($input) ) {
            $this->setInput($input);
        }
        
        if ( ! is_null($output) ) {
            $this->setOutput($output);
        }

        $this->init();
    }

    /**
     * Initialize method.
     *
     * @return void
     */
    protected function init()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->table = $this->table($this->name, $this->options, $this->adapter);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setMigratingUp($goingUp)
    {
        $this->isMigratingUp = $goingUp;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isMigratingUp()
    {
        return $this->isMigratingUp;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($sql)
    {
        return $this->getAdapter()->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function query($sql)
    {
        return $this->getAdapter()->query($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow($sql)
    {
        return $this->getAdapter()->fetchRow($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($sql)
    {
        return $this->getAdapter()->fetchAll($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($table, $data)
    {
        // convert to table object
        if (is_string($table)) {
            $table = new Table($table, array(), $this->getAdapter());
        }
        $table->insert($data)->save();
    }

    /**
     * {@inheritdoc}
     */
    public function createDatabase($name, $options)
    {
        $this->getAdapter()->createDatabase($name, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function dropDatabase($name)
    {
        $this->getAdapter()->dropDatabase($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTable($tableName)
    {
        return $this->getAdapter()->hasTable($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function table($tableName, $options = array())
    {
        return new Table($tableName, $options, $this->getAdapter());
    }

    /**
     * A short-hand method to drop the given database table.
     *
     * @param string $tableName Table Name
     * @return void
     */
    public function dropTable($tableName)
    {
        $this->table($tableName)->drop();
    }
}