<?php

namespace Core\Exceptions;

class Exception extends \Exception implements \Throwable
{
    /**
     * Exception message
     *
     * @var string
     */
    protected $message;

     /**
     * Exception code
     *
     * @var string
     */
    protected $code;

    /**
     * Exception file
     *
     * @var string
     */
    protected $file;

    /**
     * Exception line
     *
     * @var string
     */
    protected $line;

    /**
     * Exception trace
     *
     * @var array
     */
    protected $trace;

    /**
     * Previous exception
     *
     * @var Throwable
     */
    protected $previous;

    /**
     * Contructor class
     *
     * @param string $message Message of exception
     * @param int $code Exception code
     * @param \Exception $exception Previous exception
     * @return Core\Exceptions\Exception
     */
    public function __construct($message, $code = 0, Excetion $previous = null)
    {
        $this->message = $message;
    }

    /**
     * Diplay a friendly text message of the expetion
     *
     * @return void
     */
    public function __toString()
    {
        echo "
            <p style='color: red'>{$this->message}<p>
        ";
        echo "<pre>";

        foreach($this->getTrace() as $trace) {
            var_dump($trace);
        }

        echo "<pre>";
    }
}
