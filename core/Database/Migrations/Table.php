<?php

namespace Core\Database\Migrations;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table as PhinxTable;

class Table extends PhinxTable
{
    /**
     * Table is booted.
     *
     * @var bool
     */
    private static $booted = false;

    /**
     * Singleton table instance.
     *
     * @var Core\Database\Migrations\Table
     */
    private static $instance;

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * Boot table as singleton design.
     *
     * @return Core\Database\Migrations\Table
     */
    public static function boot()
    {
        if (!self::$booted) {
            self::$booted = true;
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Create a table configurating it on callback.
     *
     * @param string   $name     Table name
     * @param callable $callback Function to execute
     *
     * @return void
     */
    public static function createFrom(string $name, callable $callback)
    {
        $instance = self::boot();
        $instance->setName($name);
        call_user_func_array($callback, [&$instance]);
        $instance->create();
        self::$booted = false;
    }

    /**
     * Update from table configurating it on callback.
     *
     * @param string   $name     Table name
     * @param callable $callback Function to execute
     *
     * @return void
     */
    public static function updateFrom(string $name, callable $callback)
    {
        $instance = self::boot();
        $instance->setName($name);
        call_user_func_array($callback, [&$instance]);
        $instance->update();
        self::$booted = false;
    }

    /**
     * Drop table if it exists.
     *
     * @param string $name Table name
     *
     * @return void
     */
    public static function dropIfExists(string $name)
    {
        $instance = self::boot();
        $instance->setName($name);

        if ($instance->exists()) {
            $instance->drop();
        }

        self::$booted = false;
    }

    /**
     * Sets the database adapter.
     *
     * @param AdapterInterface $adapter Database Adapter
     *
     * @return Table
     */
    public static function setAdapterOnCurrent(AdapterInterface $adapter)
    {
        static::$instance->setAdapter($adapter);

        return static::$instance;
    }

    /**
     * Add an integer column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function integer(string $column, array $options = [])
    {
        $this->addColumn($column, 'integer', $options);

        return $this;
    }

    /**
     * Add a string column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function string(string $column, array $options = [])
    {
        $this->addColumn($column, 'string', $options);

        return $this;
    }

    /**
     * Add a biginteger column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function biginteger(string $column, array $options = [])
    {
        $this->addColumn($column, 'biginteger', $options);

        return $this;
    }

    /**
     * Add a binary column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function binary(string $column, array $options = [])
    {
        $this->addColumn($column, 'binary', $options);

        return $this;
    }

    /**
     * Add a boolean column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function boolean(string $column, array $options = [])
    {
        $this->addColumn($column, 'boolean', $options);

        return $this;
    }

    /**
     * Add a date column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function date(string $column, array $options = [])
    {
        $this->addColumn($column, 'date', $options);

        return $this;
    }

    /**
     * Add a datetime column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     */
    public function datetime(string $column, array $options = [])
    {
        $this->addColumn($column, 'datetime', $options);

        return $this;
    }

    /**
     * Add a decimal column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function decimal(string $column, array $options = [])
    {
        $this->addColumn($column, 'decimal', $options);

        return $this;
    }

    /**
     * Add a float column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function float(string $column, array $options = [])
    {
        $this->addColumn($column, 'float', $options);

        return $this;
    }

    /**
     * Add a text column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function text(string $column, array $options = [])
    {
        $this->addColumn($column, 'text', $options);

        return $this;
    }

    /**
     * Add a time column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function time(string $column, array $options = [])
    {
        $this->addColumn($column, 'time', $options);

        return $this;
    }

    /**
     * Add a timestamp column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     */
    public function timestamp(string $column, array $options = [])
    {
        $this->addColumn($column, 'timestamp', $options);

        return $this;
    }

    /**
     * Add default timestamp column to table.
     *
     * @return Core\Database\Migrations\Table
     */
    public function timestamps()
    {
        $this->addColumn('created_at', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
        ])->addColumn('updated_at', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
            'update'  => 'CURRENT_TIMESTAMP',
        ]);

        return $this;
    }

    /**
     * Add an uuid column to table.
     *
     * @param string $column  Column name
     * @param array  $options Array with other options
     *
     * @return Core\Database\Migrations\Table
     */
    public function uuid(string $column, array $options = [])
    {
        $this->addColumn($column, 'uuid', $options);

        return $this;
    }
}
