<?php

namespace Core\Sessions;

use Carbon\Carbon;

class CSRFToken implements \Serializable
{
    /**
     * CSRF random token
     * 
     * @var string
     */
    protected $token;

    /**
     * Token time expiration in timestamp
     * 
     * @var int
     */
    protected $expiration = 300;

    /**
     * Class constructor
     * 
     * @return void
     */
    public function __construct($token=null)
    {
        $this->token = $token ?: str_random(64);
    }

    /**
     * Get class data
     * 
     * @return \StdClass
     */
    protected function getData()
    {
        $class = new \StdClass;
        $class->token = $this->token;
        $class->expiration = Carbon::now()->addSeconds($this->expires_in);
        return $class;
    }

    /**
     * Serialize class data
     * 
     * @return mixed
     */
    public function serialize()
    {
        return serialize($this->getData());
    }

    /**
     * Unserialize class data
     * 
     * @return mixed
     */
    public function unserialize(string $data)
    {
        return unserialize($data);
    }
}