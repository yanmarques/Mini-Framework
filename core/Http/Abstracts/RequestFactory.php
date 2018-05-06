<?php

namespace Core\Http\Abstracts;

abstract class RequestFactory
{
    /**
     * Get server interface.
     *
     * @return string|null
     */
    abstract protected static function getInterface();

    /**
     * Get server IP address if is set.
     *
     * @return string|null
     */
    abstract protected static function getServerAddr();

    /**
     * Get identification if server the script is being executed.
     *
     * @return string|null
     */
    abstract protected static function getHostName();

    /**
     * Get current protocol server is using
     * If not set, gets default 'HTTP'.
     *
     * @return string
     */
    abstract protected static function getProtocol();

    /**
     * Get headers.
     *
     * @return array
     */
    abstract protected static function getHeaders();

    /**
     * Get server identification.
     *
     * @return string|null
     */
    abstract protected static function getServerName();

    /**
     * Get request method.
     *
     * @return string|null
     */
    abstract protected static function getMethod();

    /**
     * Get request timestamp as Carbon class.
     *
     * @return Carbon\Carbon
     */
    abstract protected static function getRequestTime();

    /**
     * Get query string from request URI.
     *
     * @return array
     */
    abstract protected static function getRequestQueryString();

    /**
     * Get normalized request URI.
     *
     * @return array
     */
    abstract protected static function getRequestUri();

    /**
     * Get uri before query string.
     *
     * @return string|null
     */
    abstract protected static function getPathInfo();

    /**
     * Get document root.
     *
     * @return string|null
     */
    abstract protected static function getDocumentRoot();

    /**
     * Get http host header.
     *
     * @return string|null
     */
    abstract protected static function getHttpHost();

    /**
     * Get http referer header.
     *
     * @return string|null
     */
    abstract protected static function getHttpReferer();

    /**
     * Get request user agent.
     *
     * @return string|null
     */
    abstract protected static function getUserAgent();

    /**
     * Get if request uses HTTPS protocol.
     *
     * @return bool
     */
    abstract protected static function getHTTPS();

    /**
     * Get remote addr where request as done.
     *
     * @return string
     */
    abstract protected static function getRemoteAddr();

    /**
     * Get DNS host.
     *
     * @return string|null
     */
    abstract protected static function getRemoteHost();

    /**
     * Get user port.
     *
     * @return string|null
     */
    abstract protected static function getRemotePort();

    /**
     * Get server port.
     *
     * @return string|null
     */
    abstract protected static function getServerPort();
}
