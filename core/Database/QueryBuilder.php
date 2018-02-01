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
        'update' => [],
        'set' => [],
        'values' => []
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
     * Model class
     * 
     * @var Core\Database\Model
     */
    protected $model;

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
        if ( func_num_args() == 1 ) {
            throw new \InvalidArgumentException('Not enough arguments passed to function.');
        }

        if ( func_num_args() == 2 ) {

            // Handle closure
            if ( is_callable($operator) ) {
                $builder = new static($this->connection);
                call_user_func_array($operator, [&$builder]);
                $queries = $this->resolveBindings([$column => '(' . $builder->toSql() . ')']);
            } 
            
            // Handle string
            else {
                $queries = $this->resolveBindings([$column => $operator]);
            } 
        } else {
            $queries = $this->resolveBindings([$column => $comparedColumn], $operator);
        }

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
        $this->queries['where'][] =  "$column = null";
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
     * Add UPDATE query to query options 
     * 
     * @param mixed $columns Column to update
     * @return Core\Database\QueryBuilder
     */
    public function update($columns)
    {
        if ( is_array($columns) ) {
            $columns = implode(', ', $columns);
        }

        $this->queries['update'][] = $columns;
        return $this;
    }

    /**
     * Add SET query when UPDATE query 
     * 
     * @param mixed $column
     * @param mixed|null $value
     * @return Core\Database\QueryBuilder
     */
    public function set($column, $value=null)
    {
        $query = [];
        
        if ( func_num_args() == 1 ) {
            if ( ! is_array($column) ) {
                throw new QueryException('Invalid argument "column".');
            }
            
            $query = $column;
        } elseif ( func_num_args() == 2 ) {
            $query[$column] = $value;
        } else {
            throw new QueryException('Invalid arguments.');
        }

        $bindings = $this->resolveBindings($query);
        
        $this->queries['set'][] = $bindings;
        return $this;
    }

    /**
     * Add INSERT query to options indicating the column to insert
     * 
     * @param string $column Column name
     * @return Core\Database\QueryBuilder
     */
    public function insert(string $column)
    {
        $this->queries['insert'][] = $this->wrap($column);
        return $this;
    }

    /**
     * Add VALUES query when INSERT query
     * 
     * @param array $values Values to insert
     * @return Core\Database\QueryBuilder
     */
    public function values(array $values)
    {
        $fields = array_keys($values);
        $fields = $this->wrap($fields);
        
        // Resolve value bindings
        foreach($values as $value) {
            $this->queries['values'][] = $this->resolveBindings($value);
        }

        $this->queries['insert'][] = '(' .implode(', ', $fields). ')';
        return $this;
    }

    /**
     * Set builder model
     * 
     * @param Core\Database\Model $model
     * @return Core\Database\QueryBuilder
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Fetch all rows
     * 
     * @return Core\Stack\Stack
     */
    public function get()
    {
        // Models stack class
        $models = stack();

        foreach($this->prepareSql()->executeQuery() as $row) {

            // Add a created model to models list
            $models->add($this->createFromModel($row));
        }

        return $models;
    }

    /**
     * Fetch the first result from dabatase
     * 
     * @return mixed
     */
    public function first()
    {
        $result = $this->prepareSql()->executeFetch();

        // No results was found on fetch
        if ( ! $result ) {
            return null;
        }
    
        return $this->createFromModel($result);
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
     * Create a new instance of model and save it
     * 
     * @param array $attributes Attributes to save
     * @return Core\Database\Model
     */
    public function store(array $attributes)
    {
        return $this->model->newInstance($attributes)
            ->save();
    }

    /**
     * Execute INSERT, UPADTE, DELETE query on prepared statement
     * 
     * @throws \Exception
     * 
     * @return bool
     */
    public function save()
    {
        // Create a statement from query options
        $statement = $this->prepareSql();

        // Actually executes the query and return the affected rows
        $statement->executeUpdate();
        return true;
    }

    /**
     * Find a row in database by model primary key
     * 
     * @param mixed $identifier Model identifier
     * @return null|Core\Database\Model
     */
    public function find($identifier)
    {
        return $this->model->newQuery()->where($this->model->getPrimaryKey(), $identifier)
            ->first();
    }

    /**
     * Resolve all SQL bindings
     * 
     * @param mixed $bindings
     */
    private function resolveBindings($bindings, $operator='=')
    {
        $options = [];

        if ( is_array($bindings) ) {
            foreach($bindings as $key => $value) {
                $options[$this->wrap($key)] = static::MARKER;
                $this->bindings->add($value);
            }
        } else {
            $this->bindings->add($bindings);
            return static::MARKER;
        }

        $query = '';

        foreach($options as $key => $value) {
            $query .= implode(' ', [$key, $operator, $value]);
        }

        return $query;
    }

    /**
     * Wrap marker placeholder to escape sql
     * 
     * @param mixed $value Value to wrap
     * @return mixed
     */
    private function wrap($value)
    {
        if ( is_array($value) ) {
            foreach($value as $key => $data) {
                $value[$key] = $this->wrap($data);
            }

            return $value;
        }

        return '`'.$value.'`';
    }

    /**
     * Create a new model instance with datas
     * 
     * @param array $data Model attributes
     * @return Core\Database\Model
     */
    private function createFromModel(array $data)
    {
        return $this->model->newInstance($data);
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
            $query[] = $this->compileSelect(); 
        } elseif ( ! empty($this->queries['insert']) ) {
            $query[] = $this->compileInsert();
        } elseif ( ! empty('update') ) {
            $query[] = $this->compileUpdate();
        }

        if ( ! empty($this->queries['where']) ) {
            $query[] = 'WHERE ' . implode(' AND ', $this->queries['where']);
        }

        if ( ! empty($this->queries['orWhere']) ) {
            $query[] = 'OR WHERE ' . implode(' OR ', $this->queries['orWhere']);
        }

        return implode(' ', $query);
    }

    /**
     * Compile SELECT query
     * 
     * @return string
     */
    private function compileSelect()
    {
        $query = "SELECT ". implode(', ', $this->queries['select']);
        $query .= " FROM ". implode(', ', $this->queries['from']);
        return $query;
    }

    /**
     * Compile INSERT query
     * 
     * @return string
     */
    private function compileInsert()
    {
        $query = "INSERT INTO ". implode(' ', $this->queries['insert']);
        $query .= " VALUES (". implode(', ', $this->queries['values']) .")";
        return $query;
    }

    /**
     * Compile UPDATE query
     * 
     * @return string
     */
    private function comileUpdate()
    {
        $query = "UPDATE " . implode(', ', $this->queries['update']);
        $query .= " SET " . implode(', ', $this->queries['set']); 
        return $query;
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