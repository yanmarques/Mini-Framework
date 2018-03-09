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
    protected $class;

    /**
     * Class namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Instance of the class to reflect
     *
     * @var object
     */
    protected $object;

    /**
     * Use closure instead of class
     *
     * @var Closure
     */
    protected $closure;

    /**
     * Reflector class
     *
     * @var ReflectorClass
     */
    protected $reflector;

    /**
     * Class bindings are resolved
     *
     * @var bool
     */
    protected $resolved = false;

    /**
     * List of reflector methods
     *
     * @var ReflectionMethod
     */
    protected $methods = [];

    /**
     * Instance class dependecies.
     * 
     * @var array
     */
    protected $dependencies = [];

    /**
     * Repository instances to resolve object.
     * 
     * @var Core\Foundation\Repository
     */
    protected $repository;

    /**
     * Handles system files
     *
     * @var Core\Files\FileHandler
     */
    protected $fileHandler;

    /**
     * Reflect classes to access methods and properties.
     *
     * @param Core\Foundation\Repository $repository Instances repository
     * @param Core\Files\FileHandler $fileHandler Handle files
     */
    public function __construct(Repository $repository, FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
        $this->repository = $repository;
    }

    /**
     * Bind class to reflector resolver.
     *
     * @param mixed $class
     * @return $this
     */
    static function bind($class, $dependecies)
    {
        return (new static(Repository::boot(), FileHandler::boot()))
            ->resolve($class)
            ->depends($dependecies);
    }

    /**
     * Class instance depends on array dependencies.
     * 
     * @param array $dependecies Class dependencies
     * @return Core\Reflector\Reflector
     */
    public function depends(array $dependecies)
    {
        $this->dependecies = $dependecies;
        return $this;
    }

    /**
     * Get resolved object.
     * 
     * @return mixed
     */
    public function getObject()
    {
        $this->reflect();
        return $this->object;
    }

    /**
     * Get resolved closure.
     * 
     * @return \Closure
     */
    public function getClosure()
    {
        $this->reflect();
        return $this->closure;
    }

    /**
     * Get a static property from class by it's name
     *
     * @param string $name Property name
     * @return mixed
     */
    public function getStaticProperty(string $name)
    {
        $this->canGetProperty();

        // Reflector does not have such property
        if ( ! $this->hasProperty($name) ) {
            return false;
        }

        return $this->reflector->getStaticPropertyValue($name);
    }

    /**
     * Invoke reflector class method with arguments.
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
     * Invoke reflector class static method with arguments.
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
        $parent = $this->reflector->getParentClass();
        return ! $parent ? $parent : $parent->getName() == $name;
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
    public function resolve($class)
    {
        if ( gettype($class) == 'object' ) {
            $this->resolveObject($class);
        } elseif ( gettype($class) == 'string' ) {
            $this->resolveClass($class);
        } elseif ( $class instanceof Closure ) {
            $this->closure = $class;
        } else {
            $type = gettype($class);
            throw new ReflectorException("Class must be an object, string or a Closure, not [{$type}]");
        }

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
     * Resolve a class checking wheter it exists and set namespace
     *
     * @param string $class Class complete namespace with class
     * @return void
     */
    private function resolveClass(string $class)
    {
        if ( ! class_exists($class) ) {
            throw new \InvalidArgumentException("Class $class does not exists.");
        }

        $this->namespace = $this->splitNamespace($class);
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
        $class = get_class($object);
        $this->namespace = (new ReflectionClass($class))->getNamespaceName();
        $this->class = $this->splitNamespace($class);
        $this->object = $object;
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
     * Reflect object instances and classes.
     *
     * @return void
     */
    private function reflect()
    {
        if ( ! $this->resolved ) {

            // Reflector is a closure
            if ( $this->isClosure() ) {

                // Create a new reflector function object
                $this->reflector = $this->createFunction();
                $this->resolved = true;
            } else {
                
                // Create a new reflector class object
                $this->reflector = $this->createClass();
                $this->object = $this->object ?: $this->resolveInstance();
            }   
        }
    }

    /**
     * Resolve class arguments on constructor.
     * 
     * @return array
     */
    private function resolveClassArguments()
    {
        $parameters =  $this->createMethod('__construct')->getParameters();

        if ( empty($parameters) ) {
            return [];
        }

        $args = [];

        foreach($parameters as $parameter) {
            $args[] = $parameter->getClass();
        }

        return $args;
    }

    /**
     * Return an object of instance
     *
     * @return object
     */
    private function resolveInstance()
    {
        // Receive argument type on class constructor to instantiate the class
        $arguments = $this->resolveClassArguments();

        try {
            $dependecies = $this->createMethod('__construct');
            $instance = new $this->class(...$this->dependencies);
            $this->resolved = true;
            return $instance;
        } catch(\Exception $e) {

            // Indicate where has more dependencie objects to resolve.
            // Probably dependencies are not resolved too, so we will try 
            // to resolve each instance too using recursive method.
            foreach($this->dependencies as $key => $dependecy) {
                if ( $concrete = $this->repository->make($dependecy) ) {
                    $this->dependencies[$key] = $concrete;    
                } elseif ( $concrete = static::bind($dependecie)->getObject() ) {
                    $this->dependencies[$key] = $concrete;
                } else {
                    return null;
                }
            }

            try {
                // Try to resolve instance with new dependencies.
                $instance = new $this->class(...$this->dependencies);
                $this->resolved = true;
            } catch (\Exception $e) {

            }
        
            // Could not resolve class instance.
            return $instance;
        }
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
