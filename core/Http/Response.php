<?php

namespace Core\Http;

use Core\Interfaces\Http\ResponseInterface;
use Core\Http\Request;
use Core\Http\HeadersStack;
use Core\Views\View;

class Response implements ResponseInterface
{
    /**
     * Http response status
     */
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NONAUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const MOVED_TEMPORARILY = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 408;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const REQUESTURI_TOO_LARGE = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const IM_A_TEAPOT = 418;
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * Status Code with it's correspondent status text
     *
     * @var array
     */
    public static $statusTexts = [
        '100' => 'Continue',
        '101' => 'Switching Protocol',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '300' => 'Multiple Choice',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' => 'Unused',
        '307' => 'Temporary Redirect',
        '308' => 'Permanent Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Payload Too Large',
        '414' => 'URI To Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '426' => 'Upgrade Required',
        '428' => 'Precondition Required',
        '429' => 'Too Many Requests',
        '431' => 'Request Header Fields Too Large',
        '451' => 'Unavailable For Legal Reasons',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
        '511' => 'Network Authentication Required'
    ];

    /**
     * ApplicationInterface request
     *
     * @var Core\Http\Request
     */
    private $request;

    /**
     * Response content
     *
     * @var string
     */
    protected $content;

    /**
     * Response headers
     *
     * @var array
     */
    private $headers;

     /**
     * Protocol version
     *
     * @var string
     */
    private $version = '1.1';

    /**
     * View to render
     *
     * @var Core\Views\View
     */
    private $view;

    /**
     * Redirect response
     *
     * @var Core\Http\RedirectResponse
     */
    private $redirect;

    /**
     * Response status
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Response status text
     *
     * @var string
     */
    protected $statusText;

    /**
     * Constructor of class
     *
     * @param $response
     * @param int $status Status code
     * @return Core\Http\Response
     */
    public function __construct($response, int $status = 200)
    {
        $this->setStatusCode($status);
        $this->headers = $this->buildHeaders();
        $this->resolveResponse($response, $status);
    }

    /**
     * Send http response
     *
     * @return void
     */
    public function send()
    {
        ob_start();

        $this->sendHeaders()
            ->sendContent();

        ob_end_flush();

        exit();
    }

    /**
     * Send headers if not sent
     *
     * @return Core\Http\Response
     */
    private function sendHeaders()
    {
        // Headers already sent
        if ( headers_sent() ) {
            return $this;
        }

        // Response is a redirect
        if ( $this->headers->has('Location') ) {
            $location = $this->headers->pull('Location');
        }
        
         // Send response headers
        $this->headers->each(function ($value, $key) {
            header($key .": " . $value, false, $this->statusCode);
        });

        // Send HTTP protocol informations
        header(sprintf("HTTP/%s %s %s", $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);

        // Redirect to location
        if ( isset($location) ) {
            header('Location: ' . $location);
        }

        return $this;
    }

    /**
     * Send response content through buffer
     *
     * @return Core\Http\Response
     */
    private function sendContent()
    {
        if ( $this->view ) {
            $this->view->render();
        } else {
            echo $this->content;
        }

        return $this;
    }

    /**
     * Set response status
     *
     * @param int $statusCode
     * @return Core\Http\Response
     */
    public function setStatusCode(int $statusCode)
    {
        if ( ! static::validStatus($statusCode) ) {
            throw new \RuntimeException("Invalid status code [{$statusCode}]");
        }

        $this->statusCode = $statusCode;
        $this->statusText = self::$statusTexts[ (string) $statusCode];
        return $this;
    }


    /**
     * Get application default response headers
     *
     * @return array
     */
    public static function getDefaultHeaders()
    {
        return [
            'X-XSS-Protection' => '1; mode=block',
            'Content-Type' => 'text/html; charset=utf-8',
            'Transfer-Encoding' => 'gzip',
            // 'Content-Encoding' => 'gzip, compress',
            'X-Powerer-By' => 'PHP/7.1.11'
        ];
    }

    /**
     * Set default headers
     *
     * @return Core\Http\HeadersStack
     */
    private function buildHeaders()
    {
        return new HeadersStack(self::getDefaultHeaders());
    }

    /**
     * Resolve response
     *
     * @param mixed $response Response
     * @param int $status
     * @return void
     */
    private function resolveResponse($response, int $status)
    {
        if ( $response instanceof RedirectResponse ) {
            $this->resolveRedirect($response);
        } elseif ( $response instanceof View ) {
            $this->view = $response;
        } else {
            $this->content = (string) $response;
        }
    }

    /**
     * Resolve redirect response
     *
     * @param Core\Http\RedirectResponse $response
     * @return void
     */
    private function resolveRedirect(RedirectResponse $response)
    {
        $this->redirect = $response;

        if ( $response->isView() ) {
            $this->view = $response->getView();
        }

        $this->headers = $this->headers->merge($response->getHeaders());
    }

    /**
     * Verify wheter status is valid
     *
     * @param int $status Status is valid
     * @return bool
     */
    static function validStatus(int $status)
    {
        return in_array((string) $status, array_keys(self::$statusTexts));
    }

    /**
     * Verify wheter an HTTP status is success type
     *
     * @param int $status HTTP status code
     * @return bool
     */
    static function isSuccessfull(int $status)
    {
        return ($status >= 200 && $status < 300) && self::validStatus($status);
    }

    /**
     * Verify wheter an HTTP status is reading type
     *
     * @param int $status HTTP status code
     * @return bool
     */
    static function isReading(int $status)
    {
        return ($status >= 100 && $status < 200) && self::validStatus($status);
    }

    /**
     * Verify wheter an HTTP status is redirect type
     *
     * @param int $status HTTP status code
     * @return bool
     */
    static function isRedirect(int $status)
    {
        return ($status >= 300 && $status < 400) && self::validStatus($status);
    }

    /**
     * Verify wheter an HTTP status is client error type
     *
     * @param int $status HTTP status code
     * @return bool
     */
    static function isClientError(int $status)
    {
        return ($status >= 400 && $status < 500) && self::validStatus($status);
    }

    /**
     * Verify wheter an HTTP status is server error type
     *
     * @param int $status HTTP status code
     * @return bool
     */
    static function isServerError(int $status)
    {
        return ($status >= 400 && $status < 500) && self::validStatus($status);
    }
}
