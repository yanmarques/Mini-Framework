<?php

namespace Core\Support;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Exceptions\Files\FileNotFoundException;

class Creator
{
    /**
     * Creator have been booted
     * 
     * @var bool
     */
    private static $booted;

    /**
     * Singleton instance of creator
     * 
     * @var Core\Support\Creator
     */
    private static $instance;

    /**
     * ApplicationInterface to handle dependencies
     * 
     * @var Core\Interfaces\Bootstrapers\ApplicationInterface
     */
    private $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Boot creator singleton instance with application
     * 
     * @param Core\Interfaces\Bootstrapers\ApplicationInterface $app Handle dependencies
     * @return Core\Support\Creator
     */
    static function boot(ApplicationInterface $app)
    {
        if ( ! static::$booted ) {
            static::$instance = new self($app);
        }

        return static::$instance;
    }

    /**
     * Parse a stub with dummies configuration and create on specified path
     * 
     * @param string $path Path to save stub
     * @param string $stub Stub file path
     * @param array $dummies Key/value dummie configuration
     * @return void
     */
    static function parse(string $path, string $stub, array $dummies)
    {
        // Get fileHandler instance from Creator singleton
        $fileHandler = static::$instance->app->fileHandler();

        // Verify wheter $stub is a file
        // Throws exception if file is valid
        if ( ! $fileHandler->isFile($stub) ) {
            throw new FileNotFoundException("File [$stub] was not found.");
        }

        // Get content from stub file
        $stub = $fileHandler->read($stub);

        static::createFromDummies($path, $stub, $dummies);
    }

    /**
     * Create file from parse dummies
     * 
     * @param string $path File path to save
     * @param string $content Content to replace dummies
     * @param array $dummies Key/value where key is the dummie to replace with value
     * @return void
     */
    private static function createFromDummies(string $path, string $content, array $dummies)
    {
        // Get fileHandler instance from Creator singleton
        $fileHandler = static::$instance->app->fileHandler();

        // Change all dummies ocorrences for the current value
        foreach($dummies as $dummy => $value) {
            $content = str_replace($dummy, $value, $content);
        }

        $fileHandler->write($path, $content);
    }
}