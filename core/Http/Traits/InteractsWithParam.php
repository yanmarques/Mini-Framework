<?php

namespace Core\Http\Traits;

use Core\Http\RequestKernel;

trait InteractsWithParam
{   
    /**
     * Dinamically access request params
     * 
     * @return mixed|null
     */
    public function __get($name)
    {
        // Request has parameter
        if ( $this->parameters->has($name) ) {
            return $this->parameters->get($name);
        }

        // Any parameter found
        return null;
    }
}