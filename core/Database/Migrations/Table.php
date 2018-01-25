<?php

namespace Core\Database\Migrations;

use Phinx\Db\Table as PhinxTable;
use Phinx\Db\Adapter\AdapterInterface;

class Table extends PhinxTable
{   
    /**
     * Class constructor
     * 
     * @param string $name Table name
     * @param array $options Table options
     * @param Phinx\Db\Adapter\AdapterInterface $adapter Database adapter 
     */
    public function __construct(string $name, array $options, AdapterInterface $adapter)
    {
       parent::__construct($name, $options, $adapter);
    }

    /**
     * Add an integer column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function integer(string $column, array $options = [])
    {
        $this->addColumn($column, 'integer', $options);
    }

    /**
     * Add a string column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function string(string $column, array $options = [])
    {
        $this->addColumn($column, 'string', $options);
    }

    /**
     * Add a biginteger column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function biginteger(string $column, array $options = [])
    {
        $this->addColumn($column, 'biginteger', $options);
    }

    /**
     * Add a binary column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function binary(string $column, array $options = [])
    {
        $this->addColumn($column, 'binary', $options);
    }

    /**
     * Add a boolean column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function boolean(string $column, array $options = [])
    {
        $this->addColumn($column, 'boolean', $options);
    }

    
    /**
     * Add a date column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function date(string $column, array $options = [])
    {
        $this->addColumn($column, 'date', $options);
    }
    
    /**
     * Add a datetime column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function datetime(string $column, array $options = [])
    {
        $this->addColumn($column, 'datetime', $options);
    }
    
    /**
     * Add a decimal column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function decimal(string $column, array $options = [])
    {
        $this->addColumn($column, 'decimal', $options);
    }
    
    /**
     * Add a float column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function float(string $column, array $options = [])
    {
        $this->addColumn($column, 'float', $options);
    }
    
    /**
     * Add a text column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function text(string $column, array $options = [])
    {
        $this->addColumn($column, 'text', $options);
    }
    
    /**
     * Add a time column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function time(string $column, array $options = [])
    {
        $this->addColumn($column, 'time', $options);
    }
    
    /**
     * Add a timestamp column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function timestamp(string $column, array $options = [])
    {
        $this->addColumn($column, 'timestamp', $options);
    }
    
    /**
     * Add an uuid column to table
     * 
     * @param string $column Column name
     * @param array $options Array with other options
     */
    public function uuid(string $column, array $options = [])
    {
        $this->addColumn($column, 'uuid', $options);
    }
}