<?php

namespace Core\Http;

class IpUtils
{
    /**
     * Constant for unrecognized IP
     */
    const UNKNOW = 'unknown';

    /**
     * Get IP from request
     *
     * @return string
     */
    public static function ip()
    {
        if ( isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] )
            return $_SERVER['HTTP_CLIENT_IP'];

        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] )
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        if( isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'] )
            return $_SERVER['HTTP_X_FORWARDED'];

        if( isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'] )
            return $_SERVER['HTTP_FORWARDED_FOR'];

        if( isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'] )
            return $_SERVER['HTTP_FORWARDED'];

        if( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] )
            return $_SERVER['REMOTE_ADDR'];

        return IpUtils::UNKNOW;
    }

    /**
     * Check wheter an ip is version 6
     * 
     * @param string $ip
     * @return bool
     */
    public static function isV6(string $ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_VALIDATE_IPV6);
    }
}
