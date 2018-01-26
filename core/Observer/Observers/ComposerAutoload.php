<?php

namespace Core\Observer\Observers;

use Core\Interfaces\Observer\ObserverInterface;
use Symfony\Component\Process\Process;

class ComposerAutoload implements ObserverInterface
{
    /**
     * Observer name
     * 
     * @var string
     */
    private $name = 'autoload';

    /**
     * Handle observer event
     * 
     * @return void
     */
    public function handle()
    {
        (new Process('composer dumpautoload -o -a'))->run();
    }

    /**
     * Get observer name
     * 
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}