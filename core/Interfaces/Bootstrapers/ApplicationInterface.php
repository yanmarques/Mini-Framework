<?php

namespace Core\Interfaces\Bootstrapers;

use Core\Http\Request;

interface ApplicationInterface
{
    /**
     * Return application base directory
     *
     * @return string
     */
    public function baseDir();
    /**
     * Return application app directory
     *
     * @return string
     */
    public function appDir();

    /**
     * Return application core directory
     *
     * @return string
     */
    public function coreDir();

    /**
     * Return application routes directory
     *
     * @return string
     */
    public function routesDir();

    /**
     * Return application views directory
     *
     * @return string
     */
    public function viewsDir();

    /**
     * Handle an an $input argument
     *
     * @param mixed
     * @return mixed
     */
    public function handle($input, $secondary = null);

    /**
     * Executed before application boot services
     * 
     * @return void
     */
    public function booting();

    /**
     * Boot application
     * 
     * @return void
     */
    public function boot();
   
    /**
     * Executed after application has been booted
     * 
     * @return void
     */
    public function booted();
}
