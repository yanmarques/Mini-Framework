<?php

namespace Core\Interfaces\Http;

interface ResponseStatusInterface
{
    /**
     * Get response status code
     * 
     * @return int
     */
    public function getStatus();
}