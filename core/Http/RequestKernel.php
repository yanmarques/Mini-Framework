<?php

namespace Core\Http;

use Core\Http\Abstracts\RequestFactory;
use Core\Interfaces\Http\RequestKernelInterface;

class RequestKernel extends RequestFactory implements RequestKernelInterface
{
    /**
     * Http methods.
     */
    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const OPTIONS = 'OPTIONS';

    /**
     * Request factory should override global configurations.
     *
     * @var bool
     */
    protected static $overrideGlobalConfigurations = false;

    /**
     * Gateway php interface.
     *
     * @var string
     */
    protected $interface;

    /**
     * Server Ip address.
     *
     * @var string|null
     */
    protected $address;

    /**
     * Server hostname.
     *
     * @var string
     */
    protected $hostname;

    /**
     * Protocol name.
     *
     * @var string
     */
    protected $protocol;

    /**
     * Protocol name.
     *
     * @var string
     */
    protected $headers;

    /**
     * Server identification.
     *
     * @var string
     */
    protected $server;

    /**
     * Protocol method.
     *
     * @var string
     */
    protected $method;

    /**
     * Time when request was made.
     *
     * @var string
     */
    protected $timestamp;

    /**
     * Request query string.
     *
     * @var string
     */
    protected $queryString;

    /**
     * Request uri.
     *
     * @var string
     */
    protected $requestUri;

    /**
     * URI used by user before query string.
     *
     * @var string
     */
    protected $pathInfo;

    /**
     * Document root where script has run.
     *
     * @var string
     */
    protected $root;

    /**
     * Host header.
     *
     * @var string
     */
    protected $httpHost;

    /**
     * Referer header.
     *
     * @var string
     */
    protected $httpReferer;

    /**
     * Http user agent.
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Script queried through HTTPS protocol.
     *
     * @var string
     */
    protected $https;

    /**
     * User remote ip.
     *
     * @var string
     */
    protected $remoteAddress;

    /**
     * DNS host.
     *
     * @var string
     */
    protected $remoteHost;

    /**
     * Remote port used to comunicate with server.
     *
     * @var string
     */
    protected $remotePort;

    /**
     * Server port used to serve page.
     *
     * @var string
     */
    protected $serverPort;

    /**
     * GET parameters.
     *
     * @var array|null
     */
    protected $get;

    /**
     * POST parameters.
     *
     * @var array|null
     */
    protected $post;

    /**
     * COOKIE parameters.
     *
     * @var array|null
     */
    protected $cookies;

    /**
     * SESSION parameters.
     *
     * @var array|null
     */
    protected $session;

    private function __construct($interface, $address, $hostname, $protocol, $headers, $server, $method, $timestamp, $queryString, $requestUri, $pathInfo,
        $root, $httpHost, $httpReferer, $userAgent, $https, $remoteAddress, $remoteHost, $remotePort, $serverPort, $get, $post, $cookies, $session)
    {
        $this->interface = $interface;
        $this->address = $address;
        $this->hostname = $hostname;
        $this->protocol = $protocol;
        $this->headers = $headers;
        $this->server = $server;
        $this->method = $method;
        $this->timestamp = $timestamp;
        $this->queryString = $queryString;
        $this->requestUri = $requestUri;
        $this->pathinfo = $pathInfo;
        $this->root = $root;
        $this->httpHost = $httpHost;
        $this->httpReferer = $httpReferer;
        $this->userAgent = $userAgent;
        $this->https = $https;
        $this->remoteAddress = $remoteAddress;
        $this->remoteHost = $remoteHost;
        $this->serverPort = $serverPort;
        $this->remotePort = $remotePort;
        $this->get = $get;
        $this->post = $post;
        $this->cookies = $cookies;
        $this->session = $session;
    }

    /**
     * Boot request kernel and return an request.
     *
     * @return Core\Http|Request
     */
    public static function createFromGlobals()
    {
        $instance = new static(...self::globalAttributes());
        $instance->creating();

        return $instance;
    }

    /**
     * Get attributes from globals server variables.
     *
     * @return array
     */
    public static function globalAttributes()
    {
        return [
            self::getInterface(), self::getServerAddr(), self::getHostName(), self::getProtocol(),
            self::getHeaders(), self::getServername(), self::getMethod(), self::getRequestTime(),
            self::getRequestQueryString(), self::getRequestUri(), self::getPathInfo(), self::getDocumentRoot(),
            self::getHttpHost(), self::getHttpReferer(), self::getUserAgent(), self::getHttps(),
            self::getRemoteAddr(), self::getRemoteHost(), self::getRemotePort(), self::getServerPort(),
            $_GET, $_POST, $_COOKIE, self::getSession(),
        ];
    }

    /**
     * Get server interface.
     *
     * @return string|null
     */
    protected static function getInterface()
    {
        return $_SERVER['GATEWAY_INTERFACE'] ?? null;
    }

    /**
     * Get server IP address if is set.
     *
     * @return string|null
     */
    protected static function getServerAddr()
    {
        return $_SERVER['SERVER_ADDR'] ?? null;
    }

    /**
     * Get identification if server the script is being executed.
     *
     * @return string|null
     */
    protected static function getHostName()
    {
        return $_SERVER['SERVER_NAME'] ?? null;
    }

    /**
     * Get current protocol server is using
     * If not set, gets default 'HTTP'.
     *
     * @return string
     */
    protected static function getProtocol()
    {
        return $_SERVER['SERVER_PROTOCOL'] ?? 'http';
    }

    /**
     * Get headers.
     *
     * @return array
     */
    protected static function getHeaders()
    {
        return \getallheaders();
    }

    /**
     * Get server identification.
     *
     * @return string|null
     */
    protected static function getServerName()
    {
        return $_SERVER['SERVER_SOFTWARE'] ?? null;
    }

    /**
     * Get request method.
     *
     * @return string|null
     */
    protected static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get request timestamp.
     *
     * @return int
     */
    protected static function getRequestTime()
    {
        return $_SERVER['REQUEST_TIME'];
    }

    /**
     * Get query string from request URI.
     *
     * @return array
     */
    protected static function getRequestQueryString()
    {
        return $_SERVER['QUERY_STRING'] ?? '';
    }

    /**
     * Get normalized request URI.
     *
     * @return array
     */
    protected static function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }

    /**
     * Get uri before query string.
     *
     * @return string|null
     */
    protected static function getPathInfo()
    {
        return $_SERVER['PATH_INFO'] ?? '';
    }

    /**
     * Get document root.
     *
     * @return string|null
     */
    protected static function getDocumentRoot()
    {
        return $_SERVER['DOCUMENT_ROOT'] ?? null;
    }

    /**
     * Get http host header.
     *
     * @return string|null
     */
    protected static function getHttpHost()
    {
        return $_SERVER['HTTP_HOST'] ?? null;
    }

    /**
     * Get http referer header.
     *
     * @return string|null
     */
    protected static function getHttpReferer()
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Get request user agent.
     *
     * @return string|null
     */
    protected static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Get if request uses HTTPS protocol.
     *
     * @return bool
     */
    protected static function getHTTPS()
    {
        return $_SERVER['HTTPS'] ?? false;
    }

    /**
     * Get remote addr where request as done.
     *
     * @return string
     */
    protected static function getRemoteAddr()
    {
        return IpUtils::ip();
    }

    /**
     * Get DNS host.
     *
     * @return string|null
     */
    protected static function getRemoteHost()
    {
        return $_SERVER['REMOTE_HOST'] ?? null;
    }

    /**
     * Get user port.
     *
     * @return string|null
     */
    protected static function getRemotePort()
    {
        return $_SERVER['REMOTE_PORT'] ?? null;
    }

    /**
     * Get server port.
     *
     * @return string|null
     */
    protected static function getServerPort()
    {
        return $_SERVER['SERVER_PORT'] ?? null;
    }

    /**
     * Get php session.
     *
     * @return string|null
     */
    protected static function getSession()
    {
        return $_SESSION ?? null;
    }
}
