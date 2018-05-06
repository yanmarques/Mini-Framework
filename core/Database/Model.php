<?php

namespace Core\Database;

use Core\Database\Traits\HasActionEvents;
use Core\Interfaces\Database\ConnectionInterface;
use Doctrine\Common\Inflector\Inflector;

abstract class Model
{
    use HasActionEvents;

    /**
     * Connection class.
     *
     * @var Core\Database\Connection
     */
    protected $connection;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes to mass assignment.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Default created date.
     *
     * @var string
     */
    protected $created = 'created_at';

    /**
     * Default updated date.
     *
     * @var string
     */
    protected $updated = 'updated_at';

    /**
     * Indicates if primary should autoincrement.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Array with booted models.
     *
     * @var array
     */
    protected static $bootedModels = [];

    /**
     * Model attributes.
     *
     * @var array
     */
    private $attributes = [];

    /**
     * The original attributes from table.
     *
     * @var array
     */
    private $original = [];

    /**
     * Class constructor.
     *
     * @param string $name Model name
     * @param Core\Interfaces\Database\ConnectionInterface
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->setTableIfNotSet();
        $this->setConnection(Connection::boot());
        $this->setOriginal($attributes);
        $this->fill($attributes);
        $this->addDateToFillable();

        static::bootIfNotBooted();
    }

    /**
     * The boot method called when model is instantiated.
     *
     * @return void
     */
    protected static function boot()
    {
        //
    }

    /**
     * Set model connection.
     *
     * @param Core\Interfaces\Database\ConnectionInterface
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get table primary key.
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Fecth all rows from table.
     *
     * @return Core\Stack\Stack;
     */
    public static function all()
    {
        return (new static())->newQuery()->get();
    }

    /**
     * Fill attributes from model.
     *
     * @param array Attributes to set
     *
     * @return this
     */
    public function fill(array $attributes)
    {
        $newAttributes = $this->guardAttributes($attributes);
        $this->attributes = array_merge($this->attributes, $newAttributes);

        return $this;
    }

    /**
     * Return an array representation of user attributes.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Save model to database. Handle model existence on database
     * when model exists, update query will be executed with attributes
     * changes, otherwise an insert will be executed with attributes.
     *
     * @return void
     */
    public function save()
    {
        if ($this->exists()) {

            // Compares the array with visible/changeable model attributes with
            // the original attributes when model was first instantiated
            // In case both are equals, no actual changes were made
            if ($this->atrributes == $this->original) {
                return true;
            }

            // Get current changes between the model attributes and it's
            // original attributes
            $changes = array_diff($this->attributes, $this->original);

            // Do model update with only changes made between old attributes
            // and original database attributes
            $this->performUpdate($changes);
        }

        // Model is fully new and must be inserted on database via insert query
        // and use attributes to insert on database
        $this->performInsert();
    }

    /**
     * Indicates wheter the model exists in database.
     *
     * @return bool
     */
    protected function exists()
    {
        // Model has an identifier in attributes
        if (isset($this->attributes[$this->primaryKey])) {

            // Identifier is valid and has an item with identifier
            if ($this->query()->where($this->primaryKey, $this->attributes[$this->primaryKey])->first()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a new instance of model from attributes.
     *
     * @param array $attributes Attributes of model
     *
     * @return Core\Database\Model
     */
    public function newInstance(array $attributes)
    {
        return new static($attributes);
    }

    /**
     * Begin querying the model.
     *
     * @return Core\Database\QueryBuilder
     */
    public function query()
    {
        $builder = new QueryBuilder($this->connection);
        $builder->setModel($this);

        return $builder;
    }

    /**
     * Do model update against model changes.
     *
     * @param array $changes Changes between old attributes and original
     *
     * @return void
     */
    private function performUpdate(array $changes)
    {
        // No changes were made
        if (empty($changes)) {
            return $this;
        }

        // Dispatch model updating event
        $this->fireEvent('updating');

        // Actually executes model update on database, setting changes
        // passed as parameter using model "primaryKey" as identifier
        $this->query()->update($this->table)->set($changes)
            ->where($this->primaryKey, $this->attributes[$this->primaryKey])
            ->save();

        // Dispatch model updated event
        $this->fireEvent('updated', $model);
    }

    /**
     * Perform an insert action and create a new row based on model attributes.
     *
     * @return void
     */
    private function performInsert()
    {
        // Dispatch model creating event
        $this->fireEvent('creating');

        // Actually inserts model attributes into database
        $this->query()->insert($this->table)->values($this->attributes)
            ->save();

        // Dispatch model created event
        $this->fireEvent('created', $model);
    }

    /**
     * Set inicial attributes of model.
     *
     * @param array $attributes Inicial attributes
     *
     * @return void
     */
    private function setOriginal(array $attributes)
    {
        $newAttributes = $this->guardAttributes($attributes);
        $this->original = array_merge($this->attributes, $newAttributes);
    }

    /**
     * Guard model attributes against mass assignment.
     *
     * @param array $attributes Attributes to check
     *
     * @return array
     */
    private function guardAttributes(array $attributes)
    {
        $newAttributes = [];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $newAttributes[$key] = $value;
            }
        }

        return $newAttributes;
    }

    private function addDateToFillable()
    {
        if ($this->created != null) {
            $this->fillable = array_merge($this->fillable, [$this->created]);
        }

        if ($this->updated != null) {
            $this->fillable = array_merge($this->fillable, [$this->updated]);
        }
    }

    /**
     * Get query builder instance.
     *
     * @return Core\Database\QueryBuilder
     */
    private function newQuery()
    {
        return (new QueryBuilder($this->connection))->setModel($this)
            ->select()
            ->from($this->table);
    }

    /**
     * If $table attribute is not set, parse model name to table.
     *
     * @param string $model Model name
     *
     * @return void
     */
    private function setTableIfNotSet()
    {
        if ($this->table == null) {
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
     * Boot model.
     *
     * @return void
     */
    private static function bootIfNotBooted()
    {
        if (!isset(self::$bootedModels[static::class])) {
            self::$bootedModels[static::class] = true;
        }

        // Call boot model method
        static::boot();
    }

    /**
     * Set model attributes.
     *
     * @param string $name  Attribute name
     * @param mixed  $value Value to attribute
     *
     * @return mixed
     */
    public function __set($name, $value)
    {
        if (isset($this->attributes[$name])) {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * Access model attributes.
     *
     * @param string $name Attribute name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
    }

    /**
     * Dinamically create a query builder when calling not found static methods.
     *
     * @return Core\Database\QueryBuilder
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static())->newQuery()->{$method}(...$arguments);
    }
}
