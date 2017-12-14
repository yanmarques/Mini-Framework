<?php

namespace Core\Http;

use Core\Http\RequestKernel;
use Core\Http\Traits\InteractsWithParam;
use Core\Http\Traits\QueryString;
use Core\Sessions\SessionStack;

class Request extends RequestKernel
{
    use InteractsWithParam, QueryString;

    /**
     * Request is booted
     *
     * @var bool
     */
    private static $booted;

    /**
     * Request instance
     *
     * @var Core\Http\Request
     */
    private static $instance;

    /**
     * Request parameters
     *
     * @var Core\Stack\Stack
     */
    private $parameters;

    /**
     * Create an request instance from globals
     *
     * @return Core\Http\Request
     */
    public static function get()
    {
        if ( ! self::$booted ) {
            self::$instance = parent::createFromGlobals();
        }

        return self::$instance;
    }

    /**
     * Execute resolvers when request is created
     *
     * @return void
     */
    public function creating()
    {
        $this->parameters = $this->resolveParameters();
        $this->headers = $this->resolveHeaders();
        $this->cookies = $this->resolveCookies();
        $this->session = $this->resolveSession();
    }

    /**
     * Get the request method
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

     /**
     * Get the request protocol
     *
     * @return string
     */
    public function protocol()
    {
        return $this->protocol;
    }

    /**
     * Get only the request uri without query string
     *
     * @return string
     */
    public function uri()
    {
        return $this->uriWithoutQueryString($this->requestUri, $this->queryString);
    }

    /**
     * Get request uri with query string
     *
     * @return string
     */
    public function uriWithQueryString()
    {
        return $this->requestUri;
    }

    /**
     * Get all request parameters
     *
     * @return array
     */
    public function all()
    {
        return $this->parameters->all();
    }

    /**
     * Get php running interface
     *
     * @return string|null
     */
    public function interface()
    {
        return $this->interface;
    }

     /**
     * Get server ip address
     *
     * @return string|null
     */
    public function serverAddress()
    {
        return $this->address;
    }

    /**
     * Get server hostname
     *
     * @return string|null
     */
    public function hostname()
    {
        return $this->hostname;
    }

    /**
     * Get server identification name
     *
     * @return string|null
     */
    public function server()
    {
        return $this->server;
    }

    /**
     * Get timestamp when request was executed
     *
     * @return string|null
     */
    public function time()
    {
        return \Carbon\Carbon::createFromTimestamp($this->timestamp)
            ->toDateTimeString();
    }

    /**
     * Get document root where script has run
     *
     * @return string|null
     */
    public function documentRoot()
    {
        return $this->root;
    }

    /**
     * Get http host header
     *
     * @return string|null
     */
    public function host()
    {
        return $this->httpHost;
    }

    /**
     * Get http referer header
     *
     * @return string|null
     */
    public function referer()
    {
        return $this->httpReferer;
    }

    /**
     * Get http user agent header
     *
     * @return string|null
     */
    public function userAgent()
    {
        return $this->userAgent;
    }

    /**
     * Request use HTTPS protocol
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->https ? true : false;
    }

    /**
     * Get DNS lookup host
     *
     * @return string|null
     */
    public function dnsHost()
    {
        return $this->remoteHost;
    }

    /**
     * Get remote port
     *
     * @return string|null
     */
    public function serverPort()
    {
        return $this->serverPort;
    }

    /**
     * Get server port
     *
     * @return string|null
     */
    public function remotePort()
    {
        return $this->remotePort;
    }

    /**
     * Get request cookie
     *
     * @return string|null
     */
    public function cookies()
    {
        return $this->cookie->all();
    }

     /**
     * Get request session
     *
     * @return string|null
     */
    public function session()
    {
        return $this->session;
    }

    /**
     * Get request headers
     *
     * @return string|null
     */
    public function headers()
    {
        return $this->headers->all();
    }

     /**
     * Get current local ip address
     *
     * @return string|null
     */
    public function ip()
    {
        return $this->remoteAddress;
    }

    /**
     * Request has given attribute name
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return $this->parameters->has($name);
    }

    /**
     * Resolve get and/or post parameters into a custom stack
     *
     * @return Core\Stack\Stack
     */
    private function resolveParameters()
    {
        $params = [];

        // Parse get parameters if not empty
        if ( ! empty($this->get) ) {
            foreach($this->get as $key => $value) {
                $params[$key] = $value;
            }
        }

        // Parse post parameters if not empty
        if ( ! empty($this->post) ) {
            foreach($this->post as $key => $value) {
                $params[$key] = $value;
            }
        }

        return stack($params);
    }

    /**
     * Resolve get and/or post parameters into a custom Stack
     *
     * @return Core\Http\HeadersStack
     */
    private function resolveHeaders()
    {
        return new HeadersStack($this->headers);
    }

    /**
     * Resolve request cookies and
     *
     * @return Core\Http\CookieStack
     */
    private function resolveCookies()
    {
        return new CookiesStack($this->cookie);
    }

    /**
     * Resolve get and/or post parameters into a custom Stack
     *
     * @return Core\Session\SessionStack
     */
    private function resolveSession()
    {
        return app()->services()->session()->stack();
    }
}
