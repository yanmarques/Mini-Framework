<?php

namespace Core\Interfaces\Http;

use Core\Http\Request;

interface MiddlewareInterface
{
    /**
     * Apply middleware to request
     *
     * @param App\Http\Request $request
     * @return
     */
    public function apply(Request $request);
}
