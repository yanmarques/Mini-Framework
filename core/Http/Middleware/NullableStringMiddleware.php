<?php

namespace Core\Http\Middleware;

use Core\Http\Request;
use Core\Interfaces\Http\MiddlewareInterface;

class NullableStringMiddleware implements MiddlewareInterface
{
    /**
     * Apply middleware to request.
     *
     * @param App\Http\Request $request
     *
     * @return
     */
    public function apply(Request $request)
    {
        $params = [];

        foreach ($request->all() as $key => $value) {
            if ($value == '') {
                $value = null;
            }

            $params[$key] = $value;
        }

        return $request->merge($params);
    }
}
