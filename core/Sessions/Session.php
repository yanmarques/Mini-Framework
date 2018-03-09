<?php

namespace Core\Sessions;

use Core\Support\Traits\Singleton;
use SessionHandlerInterface;
use Symfony\Component\Finder\Finder;

class Session
{
    use Singleton;

    /**
     * Session handler
     * 
     * @var SessionHandlerInterface
     */
    protected $handler;

    /**
     * Session id
     * 
     * @var string
     */
    protected $id;

    /**
     * Stack class with session values
     * 
     * @var Core\Stack\Stack
     */
    protected $stack;

    /**
     * Indicates wheter session has started
     * 
     * @var bool
     */
    protected $started = false;

    /**
     * Constructor of class
     *
     * @param array $args Array with params
     * @return Core\Sessions\SessionStack
     */
    public function __construct(SessionHandlerInterface $handler)
    {
        $this->stack = stack();
        $this->handler = $handler;
    }

    /**
     * Set a session key/value and add to stack
     * 
     * @param string $key Key of session
     * @param mixed $value Key value
     * @return Core\Sessions\Session
     */
    public function set(string $key, $value)
    {
        $data = [$key => $value];
        $this->stack = $this->stack->merge($data);
        $data = $this->prepareData($data);
        $this->handler->write($this->id, $data);
        return $this;
    }

    /**
     * Unset session key
     * 
     * @param string $key Key of session
     * @return Core\Sessions\Session
     */
    public function unset(string $key)
    {
        if ( $this->stack->has($key) ) {
            $this->stack->forget($key);
            $data = $this->prepareData();
            $this->handler->write($this->id, $data);
        }

        return $this;
    }

    /**
     * Indicates wheter session has given key
     * 
     * @param string $key Session key
     * @return bool
     */
    public function has(string $key)
    {
        return $this->stack->has($key);
    }

    /**
     * Start session
     * 
     * @param string $id Session id
     * @return Core\Sesssions\Session
     */
    public function start($id=null)
    {
        if ( ! $this->started ) {
            $this->setId($id);
            $this->loadSesssion();
        }

        return $this;
    }

    /**
     * Set session id
     * 
     * @param string|null $id Session $id
     * @return void
     */
    protected function setId($id=null)
    {
        if ( $id && (is_string($id) && strlen($id) == 40) ) {
            $this->id = $id;
        } else {
            $this->id = str_random(40);
        }
    }

    /**
     * Prepares a given data to write on session handler
     * 
     * @return string
     */
    protected function prepareData($arguments=null)
    {
        // If no arguments is passed, we will use all session values
        if ( ! $arguments ) {
            $arguments = $this->stack->all();
        }

        // Encode data with json
        $data = json_encode((array) $arguments);

        // Encrypt session data if should encrypt
        if ( $this->handler->shouldEncrypt() ) {
            $data = app()->services()->crypter()->encrypt($data, false);
        }

        return $data;
    }

    /**
     * Load session from handler
     * 
     * @return void
     */
    protected function loadSession()
    {
        $sessions = $this->handler->read($this->id); 
        
        if ( ! $sessions ) {
            $this->handler->write($this->id, '');
        } else {
            if ( $this->handler->shouldEncrypt() ) {
                $sessions = app()->services()->crypter()->decrypt($sessions, false);
            }

            $this->stack = $this->stack->merge((array) json_decode($sessions));
        }
    }

    public function __get($name)
    {
        return $this->stack->get($name);
    }
}
