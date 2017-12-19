<?php

namespace Core\Reflector;

use \ReflectionClass;
use \ReflectionFunction;
use \ReflectionMethod;
use \Closure;
use Core\Reflector\Traits\CanGetProperties;
use Core\Exceptions\Reflector\ReflectorException;
use Core\Files\FileHandler;

class Reflector
{
    use CanGetProperties;

    /**
     * Class to reflect
     *
     * @var mixed
     */
    private $class;

    /**
     * Class namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * Instance of the class to reflect
     *
     * @var object
     */
    private $object;

    /**
     * Use closure instead of class
     *
     * @var Closure
     */
    private $closure;

    /**
     * Reflector class
     *
     * @var ReflectorClass
     */
    private $reflector;

    /**
     * Class bindings are resolved
     *
     * @var bool
     */
    private $resolved;

    /**
     * List of reflector methods
     *
     * @var ReflectionMethod
     */
    private $methods = [];

    /**
     * Handles system files
     *
     * @var Core\Files\FileHandler
     */
    private $fileHandler;

    /**
     * Constructor of class
     *
     * @param Core\Files\FileHandler $fileHandler Handle files
     */
    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * Bind container to given class
     *
     * @param mixed $class
     * @return $this
     */
    public function bind($class)
    {
        $this->resolveBinding($class);
        return $this;
    }

    /**
     * Get a static property from class by it's name
     *
     * @param string $name Property name
     * @return mixed
     */
    public function getProperty(string $name)
    {
        $this->canGetProperty();

        // Reflector does not have such property
        if ( ! $this->hasProperty($name) ) {
            return false;
        }

        return $this->reflector->getStaticPropertyValue($name);
    }

    /**
     * Invoke reflector class method with arguments
     *
     * @param string $name Method name
     * @param array $arguments Array with arguments
     * @return mixed
     */
    public function callMethod(string $name, array $arguments = [])
    {
        $this->resolveCall($name);
        
        return $this->reflector->getMethod($name)->invoke($this->object, ...$arguments);
    }

    /**
     * Invoke reflector class method with arguments
     *
     * @param string $name Method name
     * @param array $arguments Array with arguments
     * @return mixed
     */
    public function callStaticMethod(string $name, array $arguments = [])
    {
        $this->resolveCall($name);

        return $this->reflector->getMethod($name)->invoke(null, ...$arguments);
    }

    /**
     * Verify wheter class implements given interface
     *
     * @param string $name Interface name
     * @return bool
     */
    public function implementsInterface(string $name)
    {
        return $this->reflector->implementsInterface($name);
    }

    /**
     * Verify wheter class extends given parent class
     *
     * @param string $name Parent name
     * @return bool
     */
    public function extends(string $name)
    {
        return $this->reflector->getParentClass() == $name;
    }

    /**
     * Check wheter reflector is a closure
     *
     * @return bool
     */
    public function isClosure()
    {
        return ! $this->class && $this->closure;
    }

    /**
     * Resolve an class and returns a class string
     *
     * @param $class Class to resolve
     * @return string
     */
    private function resolveBinding($class)
    {
        if ( gettype($class) == 'object' ) {
            $this->resolveObject();
        } elseif ( gettype($class) == 'string' ) {
            $this->resolveClass($class);
        } elseif ( $class instanceof Closure ) {
            $this->closure = $class;
        } else {
            $type = gettype($class);
            throw new ReflectorException("Class must be an object, string or a Closure, not [{$type}]");
        }

        $this->resolved = true;

        $this->reflect();
    }

    /**
     * Resolve calls dependencies
     *
     * @throws ReflectorException
     *
     * @param string $name Name of method
     * @return void
     */
    private function resolveCall(string $name)
    {
        $this->canGetProperty();

        // Reflector does not have such property
        if ( ! $this->hasMethod($name) ) {
            throw new ReflectorException("No method with name [{$name}] was found on [{$this->class}].");
        }
    }

    /**
     * Get name of class object
     *
     * @param object $object Instance of object
     * @return string
     */
    private function getName($object)
    {
        return get_class($object);
    }

    /**
     * Resolve a class checking wheter it exists and set namespace
     *
     * @param string $class Class complete namespace with class
     * @return void
     */
    private function resolveClass(string $class)
    {
        $this->namespace = $this->splitNamespace($class);
        // $this->fileHandler->isClass($this->namespace .'\\'. $class);
        $this->class = $class;
    }

    /**
     * Resolve an object instance
     *
     * @param object $object Object to resolve namespace
     * @return void
     */
    private function resolveObject($object)
    {
        $class = $this->getName($object);
        $this->namespace = (new ReflectionClass($class))->getNamespaceName();
        $this->class = $this->splitNamespace($class);
    }

    /**
     * Create reflector for each class methods
     *
     * @return void
     */
    private function reflectMethods()
    {
        $this->canGetProperty();

        foreach($this->reflector->getMethods() as $method) {
            $this->methods[$method['name']] = $this->createMethod($method['name']);
        }
    }

    /**
     * Get a full namespace and get only namespace withou class
     *
     * @param string $class Class name
     * @return string
     */
    private function splitNamespace(string $class)
    {
        return substr($class, strrpos('\\', $class));
    }

    /**
     * Create reflector by class
     *
     * @return void
     */
    private function reflect()
    {
        if ( ! $this->resolved ) {
            throw new \Exception('Class bindings are not resolved. You can not reflect them.');
        }

        // Reflector is a closure
        if ( $this->isClosure() ) {

            // Create a new reflector function object
            $this->reflector = $this->createFunction();
        } else {

            // Create a new reflector class object
            $this->reflector = $this->createClass();
            $this->object = $this->resolveInstance();
        }
    }

    /**
     * Return an object of instance
     *
     * @return object
     */
    private function resolveInstance()
    {
        return $this->reflector->newInstance();
    }

    /**
     * Create reflector of class
     *
     * @return ReflectionClass
     */
    private function createClass()
    {
        return new ReflectionClass($this->class);
    }

    /**
     * Create reflector of closure
     *
     * @return ReflectionFunction
     */
    private function createFunction()
    {
        return new ReflectionFunction($this->closure);
    }

    /**
     * Create reflector of closure
     *
     * @return ReflectionMethod
     */
    private function createMethod(string $name)
    {
        return new ReflectionMethod($this->class, $name);
    }
}
