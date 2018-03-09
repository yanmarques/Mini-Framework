<?php

namespace Core\Support;

use Core\Interfaces\Bootstrapers\ApplicationInterface;
use Core\Exceptions\Files\FileNotFoundException;

class Creator
{
    use Traits\Singleton;

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

        $fileHandler->write($path, $content, true);
    }
}