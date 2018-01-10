<?php

namespace Core\Interfaces\Observer;

interface ObserverInterface
{
     /**
     * Handle observer event
     * 
     * @return void
     */
    public function handle();

    /**
     * Get observer name
     * 
     * @return string
     */
    public function name();
}