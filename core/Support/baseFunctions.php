<?php

use Core\Stack\Stack;
use Core\Http\Request;
use Core\Support\Hash;

/**
 * Thanks to ralouphie!
 *
 * @see https://github.com/ralouphie/getallheaders
 */
if ( ! function_exists('getallheaders') ) {
    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string[string] The HTTP header key/value pairs.
     */
    function getallheaders()
    {
        $headers = array();
        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }
        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        return $headers;
    }
}

if ( ! function_exists('stack') ) {
    /**
     * Create a new stack from array with data
     *
     * @param array $data Data to create stack
     * @return Core\Stack\Stack
     */
    function stack(array $data = [])
    {
        return new Stack($data);
    }
}

if ( ! function_exists('dd') ) {
    /**
     * Dump a item on browser
     *
     * @param mixed $params
     * @return void
     */
    function dd(...$params) {
        echo "<pre/>";
        var_dump(...$params);
        die;
    }
}

if ( ! function_exists('hcrypt') ) {
    /**
     * Hash a given value using php password_hash with crypt algorithm
     *
     * @param string $value Value to hash
     * @return string
     */
    function hcrypt(string $value)
    {
        return Hash::make($value);
    }
}
