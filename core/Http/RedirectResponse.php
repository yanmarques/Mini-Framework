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
     * Flashed sessions to response
     *
     * @var array
     */
    private $sessions;

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
     * Constructor of class
     *
     * @param string $path Path to view
     * @param array $sessions Flash session to response
     * @return Core\Http\RedirectResponse
     */
    public function __construct(string $url, array $sessions, int $status = 301)
    {
        $this->url = $url;
        $this->setSessions($sessions);
        $this->status($status);
        $this->headers = $this->redirectHeader();
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
    static function make(string $path, array $sessions = [], int $status = null)
    {
        return new static($path, $sessions, $status);
    }

    /**
     * Set array to flash on session
     *
     * @param array $sessions Parameters to flash
     * @return Core\Http\RedirectResponse
     */
    public function with(array $sessions)
    {
        array_merge($this->sessions, $sessions);
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
     * Get redirect headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers->all();
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
     * Set Location header to redirect
     *
     * @return Core\Stack\Stack
     */
    private function redirectHeader()
    {
        return stack([
            'Location' => $this->url
        ]);
    }

    private function setSessions(array $session)
    {
        $this->sessions = $session;

        if (app()->services()->session()->stack()->empty()) {
            app()->services()->session()->stack()
                ->add($session);
        } else {
            foreach ($session as $key => $value) {
                app()->services()->session()->stack()
                    ->add($value, $key);
            }
        }
    }
}
