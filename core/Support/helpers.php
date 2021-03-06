<?php

use Core\Support\Config;
use Core\Crypt\Crypter;
use Core\Bootstrapers\Application;
use Core\Http\RedirectResponse;
use Core\Http\Request;
use Core\Views\View;

if ( ! function_exists('app') ) {
    /**
     * Decrypt a payload with Crypter
     *
     * @param string $value Value to hash
     * @return string
     */
    function app()
    {
        return Application::instance();
    }
}

if ( ! function_exists('config') ) {
    /**
     * Get application configuration by it key name
     *
     * @param string $name
     * @return string
     */
    function config(string $name)
    {
        return app()->services()->config()->{$name};
    }
}

if ( ! function_exists('session') ) {
    /**
     * Get application session manager
     *
     * @return Core\Sessions\SessionManager
     */
    function session()
    {
        return app()->services()->session()->stack();
    }
}

if ( ! function_exists('encrypt') ) {
    /**
     * Hash a given value using php password_hash with crypt algorithm
     *
     * @param string $value Value to hash
     * @return string
     */
    function encrypt(string $value, bool $serialize = true)
    {
        return app()->services()->crypter()->encrypt($value, $serialize);
    }
}

if ( ! function_exists('decrypt') ) {
    /**
     * Decrypt a payload with Crypter
     *
     * @param string $value Value to hash
     * @return string
     */
    function decrypt(string $payload, bool $unserialize = true)
    {
        return app()->services()->crypter()->decrypt($payload, $unserialize);
    }
}

if ( ! function_exists('randomize') ) {
    /**
     * Get a secure random string
     *
     * @param string $length Length of string
     * @return string
     */
    function randomize(int $length = 16)
    {
        return Crypter::random($length);
    }
}

if ( ! function_exists('request') ) {
    /**
     * Get current application request
     *
     * @return Core\Http\Request
     */
    function request()
    {
        return Request::get();
    }
}

if ( ! function_exists('redirect') ) {
    /**
     * Get response class
     *
     * @return Core\Http\Response
     */
    function redirect(string $path = null, int $status = 302)
    {
        return RedirectResponse::make($path, $status);
    }
}

if ( ! function_exists('view') ) {
    /**
     * Get response class
     *
     * @return Core\Http\Response
     */
    function view(string $path, array $params = [])
    {
        return View::make($path)->with($params);
    }
}

if ( ! function_exists('csrf_field') ) {
    /**
     * CSRF form input with token
     *
     * @return string
     */
     function csrf_field()
     {
         $token = app()->services()->session()->CSRFToken;
         return "<input type='hidden' name='csrf_token' value='$token'>";
     }
}

if ( ! function_exists('observe') ) {
    /**
     * Call global observer
     *
     * @return string
     */
     function observe($name)
     {
        app()->services()->observer->dispatch($name);
     }
}
