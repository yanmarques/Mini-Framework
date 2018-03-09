<?php

namespace App\Exceptions;

use Core\Http\RedirectResponse;
use Core\Interfaces\Exceptions\CatcherInterface;
use Core\Files\FileHandler;

class Catcher implements CatcherInterface
{
    public function __construct($file)
    {

    }

    /**
     * Path to exceptions view.
     * 
     * @return string
     */
    public function viewsPath() 
    {
        return __DIR__ . '/app/Exceptions/views';
    }

    /**
     * Catch php errors and handle it. If null is returned the application will output a explained
     * page about the error. Any other return we will try to render the retuned value as a response.
     * You can make redirects here. 
     * 
     * @return mixed|null
     */
    public function onError(string $level, string $message, string $file, string $line)
    {
        //
    }

    /**
     * Catch application exceptions and handle it. If null is returned the application will output a explained
     * page about the error. Any other return we will try to render the retuned value as a response.
     * You can make redirects here. 
     * 
     * @return mixed|null
     */
    public function onException($e)
    {
        //
    }
}