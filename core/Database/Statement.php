<?php

namespace Core\Database;

use Doctrine\DBAL\Statement as DoctrineStatement;
use Core\Interfaces\Database\ConnectionInterface;
use Doctrine\DBAL\Types\Type;
use \PDO;

class Statement extends DoctrineStatement
{   
    /**
     * The raw sql string
     * 
     * @var string
     */
    protected $rawSql;

    /**
     * The sql string with bind values
     * 
     * @var string
     */
    protected $boundSql;

    /**
     * Array with the sql bindings
     * 
     * @var array
     */
    protected $bindings;

    /**
     * The connection class
     * 
     * @var Core\Interfaces\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * Default statement fetch mode
     * 
     * @var integer
     */
    protected $defaultFetchMode = PDO::FETCH_ASSOC;

    /**
     * Class constructor
     * 
     * @param string $sql Raw sql
     * @param array $bindings Array with sql bindings
     * @param Core\Interfaces\Database\ConnectionInterface $connection Connection class
     * @return void
     */
    public function __construct(string $sql, array $bindings, ConnectionInterface $connection)
    {
        parent::__construct($sql, $connection->getConnection());
        $this->rawSql = $sql;
        $this->boundSql = $sql;
        $this->bindings = $bindings;
        $this->connection = $connection;

        $this->resolveBindings();
        $this->useDefaultFetchMode();
        $this->execute();
    }

    /**
     * Use chaining design pattern to create an instance of class
     * 
     * @param string $sql Raw sql
     * @param array $bindings Array with sql bindings
     * @param Core\Interfaces\Database\ConnectionInterface $connection Connection class
     * @return Core\Database\Statement
     */
    static function boot(string $sql, array $bindings, ConnectionInterface $connection)
    {
        return new static($sql, $bindings, $connection);
    }

    /**
     * Return bounded sql string
     * 
     * @return string
     */
    public function toSql()
    {
        return $this->boundSql;
    }

    /**
     * Use array with bindings to bind each "?" with a value
     *      
     * @return void
     */
    protected function resolveBindings()
    {
        foreach($this->bindings as $index => $value) {

            // Binding value is integer
            if ( \is_int($index) ) {
                $index++;
            }

            $mode = Type::STRING;

            if ( is_int($value) ) {
                $mode = Type::INTEGER;
            } 

            if ( \is_bool($value) ) {
                $mode = Type::BOOLEAN;
            }

            $this->bindValue($index, $value, $mode);
            $this->boundSql = preg_replace('/\?/', '`'.$value.'`', $this->boundSql, 1);
        }
    }

    /**
     * Configure statement to use PDO default fetch mode
     * 
     * @return void
     */
    protected function useDefaultFetchMode()
    {
        $this->setFetchMode($this->defaultFetchMode);
    }
}
