<?php

namespace Core\Files;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Exceptions\Files\FileNotFoundException;
use Core\Support\Traits\Singleton;

class FileHandler
{
    use Singleton;

    /**
     * ApplicationInterface instance
     *
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    protected $baseDir;

    /**
     * Constructor of class
     *
     * @param string $baseDir Directory where handler will try files.
     * @return void
     */
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * Get file content
     * 
     * @param string $file File path
     * @return string
     */
    public function get(string $file)
    {
        return file_get_contents($file);
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
            $file = static::baseDir() . $file;

            if ( ! file_exists($file) ) {
                return false;
            }
        }

        return $file;
    }

    /**
     * Append data to file and create file if not exists
     *
     * @param string $file Absolute path to file
     * @param string $data Data to write on file
     * @return void
     */
    public function append(string $file, string $data)
    {
        return file_put_contents($file, $data, FILE_APPEND);
    }

    /**
     * Preppend data to file and create file if not exists
     *
     * @param string $file Absolute path to file
     * @param string $data Data to write on file
     * @return void
     */
    public function prepend(string $file, string $data)
    {
        if ( $this->isFile($file) ) {
            return file_put_contents($file, $data.$this->get($file));
        }

        return file_put_contents($file, $data, FILE_APPEND);
    }

    /**
     * Clear file content and write data to file
     *
     * @param string $file Absolute path to file
     * @param string $data Data to write on file
     * @return void
     */
    public function write(string $file, string $data, $recursively=false)
    {
        if ( $recursively ) {
            $paths = stack(explode(DIRECTORY_SEPARATOR, $file));

            // Parse array
            if ( $paths->first() == '' ) {
                $paths->shift();
            }

            // Remove file
            $paths->pop();

            $paths->eachSpread(function ($path) {
                $path = DIRECTORY_SEPARATOR . $path;
                if ( ! $this->isDirectory($path) ) {
                    mkdir($path);
                } 
            }, DIRECTORY_SEPARATOR);
        }

        return file_put_contents($file, $data);
    }

    /**
     * Verify wheter given directory is a valid directory
     * 
     * @param string $directory Directory path
     * @return bool
     */
    public function isDirectory(string $directory)
    {
        return is_dir($directory);
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

    /**
     * Get singleton base directory.
     * 
     * @return string
     */
    private static function baseDir()
    {
        return static::instance()->baseDir;
    }
}
