<?php

namespace Core\Exceptions\Http;

class MethodNotAllowedException extends HttpResponseException
{
    public function __construct($message)
    {
        parent::__construct($message, 415);
    }
}
