<?php

namespace Core\Support;

use Core\Interfaces\Support\HashInterface;

class Hash implements HashInterface
{
    /**
     * Default algorithm cost factor.
     *
     * @var int
     */
    protected static $cost = 10;

    /**
     * Create a SHA256 hash signed with HMAC using application key.
     *
     *
     * @param string $value Value to hash
     *
     * @throws RuntimeException
     *
     * @return string
     */
    public static function make(string $value)
    {
        $hash = password_hash($value, PASSWORD_BCRYPT, [
            'cost' => self::$cost,
        ]);

        // Hash failed
        if (!$hash) {
            throw new \RuntimeException('Hash not supported.');
        }
        return $hash;
    }

    /**
     * Compare a plain text against a hashed value.
     *
     * @param string $data   Data to compare
     * @param mixed  $hashed Hashed value
     *
     * @return bool
     */
    public static function compare(string $data, string $hashed)
    {
        return password_verify($data, $hashed);
    }
}
