<?php

namespace Core\Database;

use Core\Exceptions\Database\QueryException;
use Core\Interfaces\Database\ConnectionInterface;

class QueryBuilder
{
    /**
     * Marker to replace bindings values
     * 
     * @var string
     */
    const MARKER = '?';

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
    protected $queries = [
        'select' => [],
        'from' => [],
        'where' => [],
        'orWhere' => [],
        'insert' => [],
        'update' => []
    ];

    /**
     * Array with bindings values
     * 
     * @var Core\Stack\Stack
     */
    protected $bindings;

    /**
     * Compiler to use queries
     * 
     * @var Core\Interfaces\Database\CompilerInterface
     */
    protected $compiler;

    /**
     * Class constructor
     * 
     * @param Core\Database\ConnectionInterface
     * @return void
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
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
        if ( is_array($columns) ) {
            $columns = implode(', ', $columns);
        }
        
        $this->queries['select'][] = $this->wrap($columns);
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
        if ( is_array($columns) ) {
            $columns = implode(', ', $columns);
        }

        $this->queries['from'][] = $this->wrap($columns);
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
        $queries = [$this->wrap($column)];

        if ( func_num_args() == 1 ) {
            throw new \InvalidArgumentException('Not enough arguments passed to function.');
        }

        if ( func_num_args() == 2 ) {

            // Use default equals symbol
            $queries[] = '=';

            // Handle closure
            if ( is_callable($operator) ) {
                $builder = new static($this->connection);
                call_user_func_array($operator, [&$builder]);
                $queries[] = '(' . $builder->toSql() . ')';
            } 
            
            // Handle string
            elseif ( is_string($operator) ) {
                $queries[] = static::MARKER;
                $this->bindings->add($operator);
            } else {
                throw new QueryException('Invalid argument to WHERE clause.');
            }
        } else {
            $queries[] = $operator;
            $queries[] = static::MARKER;
            $this->bindings->add($comparedColumn);
        }

        $queries = implode(' ', $queries);

        $this->queries['where'][] = $queries;
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
        $this->queries['where'][] =  "$bindings = null";
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
        $this->queries['where'][] = "$bindings not null";
        return $this;
    }

    /**
     * Fetch all rows
     * 
     * @return Core\Stack\Stack
     */
    public function get()
    {
        return stack($this->prepareSql()->fetchAll());
    }

    /**
     * Fetch the first result from dabatase
     * 
     * @return 
     */
    public function first()
    {
        $result = $this->prepareSql()->fetch();

        // No results was found on fetch
        if ( ! $result ) {
            return null;
        }

        return stack($result);
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
                $options .= ' '. static::MARKER;
                $this->bindings->add($value);
            }
        } else {
            $options .= ' '. static::MARKER;
            $this->bindings->add($bindings);
        }

        return trim($options);
    }

    /**
     * Wrap marker placeholder to escape sql
     * 
     * @param string $value Value to wrap
     * @return string
     */
    private function wrap(string $value)
    {
        return '`'.$value.'`';
    }

    /**
     * Build query from array configuration
     * 
     * @return string
     */
    private function compileQueries()
    {
        $query = [];

        if ( ! empty($this->queries['select']) ) {
            $query[] = 'select '. implode(', ', $this->queries['select']);
            $query[] = 'from ' . implode(', ', $this->queries['from']);
        } elseif ( ! empty($this->queries['insert']) ) {
            $query[] = 'insert '. implode(', ', $this->queries['insert']);
        }

        if ( ! empty($this->queries['where']) ) {
            $query[] = 'where ' . implode(' and ', $this->queries['where']);
        }

        if ( ! empty($this->queries['orWhere']) ) {
            $query[] = 'or where ' . implode(' or ', $this->queries['orWhere']);
        }

        return implode(' ', $query);
    }

    /**
     * Prepare SQL as statement and resolve SQL string bindings
     * 
     * @return Core\Database\Statement
     */
    private function prepareSql()
    {
        $compiled = $this->compileQueries();
        return $this->connection->prepare($compiled, $this->bindings->all());
    }
}