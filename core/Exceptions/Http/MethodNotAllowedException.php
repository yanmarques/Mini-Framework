<?php

namespace Core\Exceptions\Http;

use Core\Exceptions\Http\HttpResponseException;

class MethodNotAllowed extends HttpResponseException 
{
   public function __construct($message)
   {
        parent::__construct($message, 415);
   }
}
