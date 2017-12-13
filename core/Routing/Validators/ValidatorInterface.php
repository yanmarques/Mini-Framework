<?php

namespace Core\Routing\Validators;

use Core\Routing\Route;

interface ValidatorInterface
{
    public function matches(Route $route);

    public function setNext(ValidatorInterface $validator);

    public function callNext(Route $route);
}