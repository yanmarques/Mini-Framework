<?php

namespace Core\Support;

use Core\Exceptions\Files\FileNotFoundException;
use Core\Bootstrapers\Application;
use Core\Crypter\Crypter;

class Config
{
    /**
     * Config is booted
     *
     * @var bool
     */
    private static $booted;

    /**
     * Singleton instance of class
     *
     * @var Core\Support\Config
     */
    private static $instance;

    /**
     * Application
     *
     * @var Core\Bootstrapers\Application
     */
    private $app;

    /**
     * File handler class
     *
     * @var Core\Files\FileHandler
     */
    private $fileHandler;

    /**
     * Stack with application configuration
     *
     * @var Core\Stack\Stack
     */
    private $configuration;

    /**
     * Constructor of class
     *
     * @param Core\Bootstrapers\Application $app
     * @return Core\Support\Config
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->fileHandler = $app->fileHandler();
    }

    /**
     * Boot config class
     *
     * @param Core\Bootstrapers\Application $app
     * @return Core\Support\Config
     */
    public static function boot(Application $app)
    {
        if ( ! self::$booted ) {
            self::$instance = new self($app);
        }

        return self::$instance;
    }

    /**
     * Get config instance
     * If config is not booted, will return null
     *
     * @return null|Core\Support\Config
     */
    public static function instance()
    {
        return self::$instance;
    }

    /**
     * Resolve application file configuration
     *
     * @return void
     */
    public function resolveConfiguration()
    {
        $path = $this->app->baseDir() .DIRECTORY_SEPARATOR. '.env';

        // Configuration not exists
        if ( ! $this->fileHandler->isFile($path) ) {
            $this->create($path);
        }

        // Set configuration
        $this->configuration = $this->renderFileContent(
            $this->fileHandler->read($path)
        );
    }

    /**
     * Create configuration file
     *
     * @param string $path Path to configuration
     * @return void
     */
    private function create(string $path)
    {
        $this->fileHandler->write($path, $this->renderTemplate());
    }

    /**
     * Generate application key
     *
     * @return string
     */
    private function generateKey()
    {
        return $this->app->services()->crypter()->getKey();
    }

    /**
     * Render configuration template
     *
     * @return string
     */
    private function renderTemplate()
    {
        $data = [];

        foreach($this->template() as $key => $value) {
            $data[] = $key .'='. $value;
        }

        return implode("\n", $data);
    }

    /**
     * Render configuration from file content
     *
     * @return Core\Stack\Stack
     */
    private function renderFileContent(string $content)
    {
        // Explode content by break line
        $content = explode("\n", $content);
        $data = [];

        // Iterate into each value
        foreach($content as $value) {
            $value = explode('=', $value);

            // If key has a value
            if ( isset($value[1]) ) {
                $data[strtolower($value[0])] = $value[1];
            }
        }

        return stack($data);
    }

    /**
     * Get configuration template
     *
     * @return string
     */
    private function template()
    {
        return [
            'KEY' => $this->generateKey(),
            'DOMAIN' => 'localhost'
        ];
    }

    /**
     * Dinamically access configuration stack
     *
     * @param mixed $name Configuration key
     * @return mixed|null
     */
    public function __get($name)
    {
        if ( $this->configuration->has($name) ) {
            return $this->configuration->get($name);
        }

        return null;
    }
}
