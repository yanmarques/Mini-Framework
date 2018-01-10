<?php

namespace Core\Http\Middleware;

use Core\Http\Request;
use Core\Interfaces\Http\MiddlewareInterface;
use Core\Exceptions\Http\CSRFException;
use Core\Crypt\Crypter;

class CSRFMiddleware implements MiddlewareInterface
{
    /**
     * Apply middleware to request
     *
     * @param App\Http\Request $request
     * @return
     */
    public function apply(Request $request)
    {
        $tokenMatchs = $this->tokenMatchs($request);
        $request->session()->set('CSRFToken', Crypter::random(64));

        if ( ! $request->isShow() && ! $tokenMatchs ) {
            throw new CSRFException("Cross Site Forgery Request exception.");
        }

        return $request;
    }

    /**
     * Verify wheter token from request matchs CSRFToken from session
     *
     * @param Core\Http\Request $request Request
     * @return bool
     */
    private function tokenMatchs(Request $request)
    {
        return $request->has('csrf_token') && is_string($request->csrf_token) &&
            $request->csrf_token == $request->session()->CSRFToken;
    }
}
