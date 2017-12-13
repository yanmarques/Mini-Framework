<?php

namespace Core\Interfaces\Support;

interface HashInterface
{
    /**
     * Create a SHA256 hash signed with HMAC using application key
     * 
     * @throws RuntimeException
     * 
     * @param string $value Value to hash
     * @return string
     */
    public static function make(string $value);

    /**
     * Compare a plain text against a hashed value
     * 
     * @param string $data Data to compare
     * @param mixed $hashed Hashed value 
     * @return bool
     */
    public static function compare(string $data, string $hashed);
}