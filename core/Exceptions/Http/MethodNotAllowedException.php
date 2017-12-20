<?php

namespace Core\Exceptions\Http;

use Core\Exceptions\Http\HttpResponseException;

class MethodNotAllowedException extends HttpResponseException 
{
   public function __construct($message)
   {
        parent::__construct($message, 415);
   }
}
