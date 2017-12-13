<?php

namespace Core\Reflector\Traits;

trait CanGetProperties
{   
    /**
     * Check wheter method can get reflection class properties
     * 
     * @throws Core\Exceptions\Reflector\ReflectException
     * 
     * @param bool|false $throw Should throw exception if can not get properties
     * @return bool
     */
    private function canGetProperty(bool $throw = true)
    {
        // Reflector has not resolved bindings an is a Closure
        if ( ! $this->resolved && $this->isClosure() ) {
            if ( $throw ) {
                throw new ReflectorException('You can not get properties. Bindings are not resolved or reflector is not a class');
            }

            return false;
        }

        return true;
    }

    /**
     * Check wheter reflector has given property
     * 
     * @param string $name Name of property
     * @return bool
     */
    private function hasProperty($name)
    {
        $this->canGetProperty();
        return $this->reflector->hasProperty($name);
    }

     /**
     * Check wheter reflector has given method
     * 
     * @param string $name Name of method
     * @return bool
     */
    private function hasMethod($name)
    {
        $this->canGetProperty();
        return $this->reflector->hasMethod($name);
    }
}