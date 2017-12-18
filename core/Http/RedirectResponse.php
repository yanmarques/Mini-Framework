<?php

namespace Core\Http;

use Core\Views\View;

class RedirectResponse
{
    /**
     * Url to redirect
     *
     * @var string
     */
    private $url;

    /**
     * Redirect status
     *
     * @var int
     */
    private $status;

    /**
     * Custom headers to redirect
     *
     * @var Core\Stack\Stack
     */
    private $headers;

    /**
     * View to redirect
     * 
     * @var Core\Views\View
     */
    private $view;

    /**
     * Check wheter is a redirect to view 
     * 
     * @var bool
     */
    protected $viewRedirect = false;

    /**
     * Constructor of class
     *
     * @param string $path Path to view
     * @param array $sessions Flash session to response
     * @return Core\Http\RedirectResponse
     */
    public function __construct(string $url = null, int $status = 301)
    {
        $this->url = $url;
        $this->status($status);
        $this->headers = stack();
    }

    /**
     * Create a redirect response with chainability pattern
     *
     * Redirects with flush last session and set a new session.
     * Default redirects status code are 302
     *
     * @param string $path Path to view
     * @param array $sessions Flash session to response
     * @return Core\Http\RedirectResponse
     */
    static function make(string $path = null, int $status = 301)
    {
        return new static($path, $status);
    }

    /**
     * Set path to view
     *
     * @param string $path Relative path to view
     * @return Core\Http\RedirectResponse
     */
    public function toView(string $path, array $params = [])
    {
        $this->view = View::make($path)->with($params);
        $this->viewRedirect = true;
        return $this;
    }

    /**
     * Check wheter is a redirect to view
     * 
     * @return bool
     */
    public function isView()
    {
        return $this->viewRedirect;
    }

    /**
     * Get redirect view
     * 
     * @return Core\Views\View
     */
    public function getView()  
    {
        return $this->view;
    }

    /**
     * Get redirect headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers->merge($this->redirectHeader())
            ->all();
    }

     /**
     * Get redirect status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set array with parameters
     *
     * @param array $params Parameters to be visible on view
     * @return Core\Http\RedirectResponse
     */
    public function with(array $params)
    {
        $this->view->with($params);
        return $this;
    }

    /**
     * Set redirect status
     *
     * @param int $status Redirect status code
     */
    public function status(int $status)
    {
        if ( ! Response::isRedirect($status) ) {
            throw new \RuntimeException("Invalid redirect status.");
        }

        $this->status = $status;
        return $this;
    }

    /**
     * Use custom response header
     *
     * @param string $key Header key
     * @param string $value Header value
     * @return Core\Http\Response
     */
    public function withHeader(string $key, string $value)
    {
        $this->headers = $this->headers->merge([$key => $value]);
        return $this;
    }

    /**
     * Use custom response headers
     *
     * @param array $headers
     * @return Core\Http\Response
     */
    public function withHeaders(array $headers)
    {
        $this->headers = $this->headers->merge($headers);
        return $this;
    }

    /**
     * Set Location header to redirect
     *
     * @return Core\Stack\Stack
     */
    private function redirectHeader()
    {
        if ( $this->viewRedirect ) {
            return [];
        }

        if ( ! $this->url ) {
            throw new \RuntimeException("Redirect url must not be null.");
        }

        return stack([
            'Location' => $this->url
        ]);
    }
}
