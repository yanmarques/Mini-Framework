<?php

namespace Core\Interfaces\Http;

interface RequestKernelInterface
{
    /**
     * Boot request kernel and return an request.
     *
     * @return Core\Http|Request
     */
    public static function createFromGlobals();

    /**
     * Get attributes from globals server variables.
     *
     * @return array
     */
    public static function globalAttributes();
}
