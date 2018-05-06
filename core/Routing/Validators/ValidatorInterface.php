<?php

namespace Core\Routing\Validators;

use Core\Routing\Route;

interface ValidatorInterface
{
    public function matches(Route $route);

    public function setNext(self $validator);

    public function callNext(Route $route);
}
