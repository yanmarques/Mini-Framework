<?php

namespace Core\Database\Migrations;

use Phinx\Db\Table as PhinxTable;

class Table
{   
    /**
     * Phinx database table
     * 
     * @var Phinx\Db\Table
     */
    private $table;

    public function __construct(string $name, array $options, $adapter)
    {
        $this->table = new PhinxTable($name, $options, $adapter);
    }

    /**
     * Add an integer column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function integer(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'integer', $options);
    }

    /**
     * Add a string column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function string(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'string', $options);
    }

    /**
     * Add a biginteger column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function biginteger(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'biginteger', $options);
    }

    /**
     * Add a binary column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function binary(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'binary', $options);
    }

    /**
     * Add a boolean column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function boolean(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'boolean', $options);
    }

    
    /**
     * Add a date column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function date(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'date', $options);
    }
    
    /**
     * Add a datetime column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function datetime(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'datetime', $options);
    }
    
    /**
     * Add a decimal column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function decimal(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'decimal', $options);
    }
    
    /**
     * Add a float column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function float(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'float', $options);
    }
    
    /**
     * Add a text column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function text(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'text', $options);
    }
    
    /**
     * Add a time column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function time(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'time', $options);
    }
    
    /**
     * Add a timestamp column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function timestamp(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'timestamp', $options);
    }
    
    /**
     * Add an uuid column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function uuid(string $column, array $options = [])
    {
        $this->table->addColumn($column, 'uuid', $options);
    }
}