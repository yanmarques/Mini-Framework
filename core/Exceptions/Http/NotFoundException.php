<?php

namespace Core\Exceptions\Http;

class NotFoundException extends HttpResponseException
{
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }
}
