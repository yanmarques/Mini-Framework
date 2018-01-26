<?php

namespace Core\Database;

use Core\Interfaces\Database\ConnectionInterface;
use Doctrine\Common\Inflector\Inflector;

abstract class Model
{
    /**
     * Connection class
     * 
     * @var Core\Database\Connection
     */
    protected $connection;

    /**
     * The table name
     * 
     * @var string
     */
    protected $table;

    /**
     * The attributes to mass assignment
     * 
     * @var array
     */
    protected $fillable = [];

    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The type of the primary key
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if primary should autoincrement
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Array with booted models
     * 
     * @var array
     */
    protected static $bootedModels = [];

    /**
     * Class constructor
     * 
     * @param string $name Model name
     * @param Core\Interfaces\Database\ConnectionInterface
     * @return void
     */
    public function __construct(array $attributes = [])
    {    
        $this->setTableIfNotSet();
        $this->setConnection(Connection::boot());
        // $this->fill($attributes);

        static::bootIfNotBooted();
    }

    /**
     * Set model connection
     * 
     * @param Core\Interfaces\Database\ConnectionInterface
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fecth all rows from table
     * 
     * @return Core\Stack\Stack;
     */
    public static function all()
    {
        return (new static)->newQuery()->get();
    }

    /**
     * Get query builder instance
     * 
     * @return Core\Database\QueryBuilder
     */
    private function newQuery()
    {
        return (new QueryBuilder($this->connection))->select()
            ->from($this->table);
    }

    /**
     * If $table attribute is not set, parse model name to table
     * 
     * @param string $model Model name
     * @return void
     */
    private function setTableIfNotSet()
    {
        if ( $this->table == null ) {
            // Match strings with 
            $model = stack(explode('\\', static::class))->last();
            preg_match_all('([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)', $model, $matches);

            // Array matches
            $matchedModel = $matches[0];
            
            // Transform model name
            foreach ($matchedModel as &$match) {
                $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
            }

            $this->table = Inflector::pluralize(implode('_', $matchedModel));
        }
    }

    /**
     * Boot model
     * 
     * @return void
     */
    private static function bootIfNotBooted()
    {
        if ( ! isset(self::$bootedModels[static::class]) ) {
            self::$bootedModels[static::class] = true;
        }
    }

    /**
     * Dinamically create a query builder when calling not found static methods 
     * 
     * @return Core\Database\QueryBuilder
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static)->newQuery()->{$method}(...$arguments);
    }
}

