<?php

namespace Core\Exceptions\Http;

use Core\Exceptions\Exception;

class HttpResponseException extends Exception
{
    /**
     * Http status
     * 
     * @var int
     */
    protected $status;

    public function __construct($message, int $status)
    {
        parent::__construct($message, 0, null);
        $this->status = $status;
    }

    /**
     * Get http status
     * 
     * @return int
     */
    public function getStatus()
    {
        return $status;
    }
}
