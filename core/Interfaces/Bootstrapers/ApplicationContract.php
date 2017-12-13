<?php

namespace Core\Interfaces\Bootstrapers;

use Core\Http\Request;

interface ApplicationInterface
{
    /**
     * Handle an incomming request
     *
     * @param Core\Http\Request $request Incomming request
     * @return Core\Http\Response
     */
    public function handle(Request $request);
}
