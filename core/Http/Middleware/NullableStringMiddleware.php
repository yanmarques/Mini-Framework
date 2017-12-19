<?php

namespace Core\Http\Middleware;

use Core\Http\Request;
use Core\Interfaces\Http\MiddlewareInterface;

class NullableStringMiddleware implements MiddlewareInterface
{
    /**
     * Apply middleware to request
     *
     * @param App\Http\Request $request
     * @return
     */
    public function apply(Request $request)
    {
        return $request;
    }
}
