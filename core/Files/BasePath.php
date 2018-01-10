<?php

namespace Core\Files;

trait BasePath
{
    /**
     * Get path to services configuration
     *
     * @return string
     */
    public function servicesConfigPath()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'services.php';
    }

    /**
     * Get path to encryption configuration
     *
     * @return string
     */
    public function encryptionConfigPath()
    {
        return $this->baseDir . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'encryption.php';
    }

    /**
     * Get path to observer configuration
     *
     * @return string
     */
    public function observerConfigPath()
    {
        return $this->baseDir . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'observed.php';
    }

    /**
     * Get path to encryption configuration
     *
     * @return string
     */
    public function middlewareConfigPath()
    {
        return $this->baseDir . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'middleware.php';
    }

     /**
     * Get path to encryption configuration
     *
     * @return string
     */
    public function databaseConfigPath()
    {
        return $this->baseDir . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'database.php';
    }

    /**
     * Return application app directory
     *
     * @return string
     */
    public function appDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'app' .DIRECTORY_SEPARATOR;
    }

    /**
     * Return application core directory
     *
     * @return string
     */
    public function coreDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'core' .DIRECTORY_SEPARATOR;
    }

    /**
     * Return application routes directory
     *
     * @return string
     */
    public function routesDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'routes' .DIRECTORY_SEPARATOR;
    }

    /**
     * Return application views directory
     *
     * @return string
     */
    public function viewsDir()
    {
        return $this->baseDir .DIRECTORY_SEPARATOR. 'views' .DIRECTORY_SEPARATOR;
    }
}