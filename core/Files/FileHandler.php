<?php

namespace Core\Files;

use Core\Bootstrapers\Application;
use Core\Exceptions\Files\FileNotFoundException;

class FileHandler
{
    /**
     * Application instance
     *
     * @var Core\Bootstrapers\Application
     */
    private $application;

    /**
     * Constructor of class
     *
     * @param Core\Bootstrapers\Application
     * @return Core\Files\FileHandler
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Check wheter class exists
     *
     * @throws Core\Exceptions\Files\FileException
     *
     * @param string $file File name
     * @param string $class Class name
     * @param bool $throw Should throw exception if not a class
     * @return bool
     */
    public function isClass(string $file, string $class, bool $throw = true)
    {
        $this->require($file);

        // No class found
        if ( ! class_exists($this->getClassFromName($class)) ) {
            if ( $throw ) {
                throw new ClassNotFoundException("Class [$class] does not exists on {$file}");
            }

            return false;
        }

        return true;
    }

    /**
     * Get content of a require file
     *
     * @param string $class File path name
     * @return mixed
     */
    public function getRequiredContent(string $file)
    {
        // File was not found
        if ( ! $file = $this->isFile($file) ) {
            throw new FileNotFoundException("File [$file] not found.");
        }

        return require_once $file;
    }

    /**
     * Require a php script
     *
     * @param string $class File path name
     * @return mixed
     */
    public function require(string $file)
    {
        // File was not found
        if ( ! $file = $this->isFile($file) ) {
            throw new FileNotFoundException("File [$file] not found.");
        }

        require $file;
    }

    /**
     * Include a php script
     *
     * @param string $class File path name
     * @return mixed
     */
    public function include(string $file)
    {
        // File was not found
        if ( ! $isFile = $this->isFile($file) ) {
            throw new FileNotFoundException("File [$file] not found.");
        }

        include $isFile;
    }

    /**
     * Check wheter file exists
     *
     * @param string $file File path
     * @return bool
     */
    public function isFile(string $file)
    {
        // Try file
        if ( ! file_exists($file) ) {

            // Try file with application base uri
            $file = $this->application->baseDir() . $file;

            if ( ! file_exists($file) ) {
                return false;
            }
        }

        return $file;
    }

    /**
     * Write data to file and create file if not exists
     *
     * @param string $file Absolute path to file
     * @param string $data Data to write on file
     * @return void
     */
    public function write(string $file, string $data)
    {
        $handler = fopen($file, 'a');

        try {
            fwrite($handler, $data);
        } catch ( \Exception $e ) {}
        finally {
            fclose($handler);
        }
    }

     /**
     * Read data from file
     *
     * @param string $file Absolute path to file
     * @return mixed|false
     */
    public function read(string $file)
    {
        $handler = fopen($file, 'r');
        $status = true;

        try {
            $content = fread($handler, filesize($file));
        } catch ( \Exception $e ) {
            $status = false;
        }
        finally {
            fclose($handler);
        }

        return ! $status ?: $content;
    }

    /**
     * Get class from name
     *
     * @param string $name Name of class with namespace
     * @return string
     */
    private function getClassFromName(string $name)
    {
        return substr($name, strrpos('\\', $name));
    }
}
