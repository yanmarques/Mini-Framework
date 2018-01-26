<?php

namespace Core\Database;

use Core\Exceptions\Database\QueryException;
use Core\Interfaces\Database\ConnectionInterface;

class QueryBuilder
{
    /**
     * The connection class
     * 
     * @var Core\Interfaces\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * Array with queries statements
     * 
     * @var Core\Stack\Stack
     */
    protected $queries;

    /**
     * Array with bindings values
     * 
     * @var Core\Stack\Stack
     */
    protected $bindings;

    /**
     * The table to select
     * 
     * @var array
     */
    protected $from;

    /**
     * The columns to select
     * 
     * @var array
     */
    protected $select;

    /**
     * Class constructor
     * 
     * @param Core\Database\ConnectionInterface
     * @return void
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->queries = stack();
        $this->bindings = stack();
    }

    /**
     * Add SELECT query to builder
     * 
     * @param array $columns Columns to select
     * @return Core\Database\QueryBuilder
     */
    public function select($columns=['*'])
    {
        if ( is_string($columns) ) {
            $columns = (array) $columns;
        }

        $this->select = $columns;
        return $this;
    }

    /**
     * Add FROM querie to builder
     * 
     * @param string|array $columns Columns
     * @return Core\Database\QueryBuilder
     */
    public function from($columns)
    {
        if ( is_string($columns) ) {
            $columns = (array) $columns;
        }

        $this->from = $columns;
        return $this;
    }

    /**
     * Add WHERE querie to builder
     * 
     * @param string|array $columns Columns
     * @return Core\Database\QueryBuilder
     */
    public function where(string $column, $operator=null, $comparedColumn=null)
    {
        $querie = [];
        $querie[] = '?';
        $this->bindings->add($column);

        if ( func_num_args() == 1 ) {
            throw new \InvalidArgumentException('Not enough arguments passed to function.');
        }

        if ( func_num_args() == 2 ) {

            // Use default equals symbol
            $querie[] = '=';

            // Handle closure
            if ( is_callable($operator) ) {
                $builder = new static($this->connection);
                call_user_func_array($operator, [&$builder]);
                $querie[] = '(' . $builder->toSql() . ')';
            } 
            
            // Handle string
            elseif ( is_string($operator) ) {
                $querie[] = '?';
                $this->bindings->add($operator);
            } else {
                throw new QueryException('Invalid argument to WHERE clause.');
            }
        } else {
            $querie[] = $operator;
            $querie[] = '?';
            $this->bindings->add($comparedColumn);
        }

        $querie = implode(' ', $querie);

        $this->queries->add("where $querie");
        return $this;
    }

    /**
     * Add WHERE NULL querie to builder
     * 
     * @param string|array $columns Columns
     * @return Core\Database\QueryBuilder
     */
    public function whereNull(string $column)
    {
        $bindings = $this->resolveBindings($column);
        $this->queries->add("where $bindings = null");
        return $this;
    }

    /**
     * Add WHERE NOT NULL querie to builder
     * 
     * @param string|array $columns Columns
     * @return Core\Database\QueryBuilder
     */
    public function whereNotNull(string $column)
    {
        $bindings = $this->resolveBindings($column);
        $this->queries->add("where $bindings not null");
        return $this;
    }

    /**
     * Fetch all rows
     * 
     * @return Core\Stack\Stack
     */
    public function get()
    {
        return $this->prepareSql();
    }

    /**
     * Build querie to use as statement
     * 
     * @return string
     */
    public function toSql()
    {
        return $this->prepareSql()->toSql();
    }

    /**
     * Resolve all SQL bindings
     * 
     * @param mixed $bindings
     */
    private function resolveBindings($bindings)
    {
        $options = '';

        if ( is_array($bindings) ) {
            foreach($bindings as $value) {
                $options .= ' ?';
                $this->bindings->add($value);
            }
        } else {
            $options .= ' ?';
            $this->bindings->add($bindings);
        }

        return trim($options);
    }

    /**
     * Prepare SQL as statement and resolve SQL string bindings
     * 
     * @return Core\Database\Statement
     */
    private function prepareSql()
    {
        $compiled = (new Compiler($this->select, $this->from, $this->queries))->compileToSql();
        return $this->connection->prepare($compiled, $this->bindings->all());
    }
}