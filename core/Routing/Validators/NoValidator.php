<?php

namespace Core\Routing\Validators;

use Core\Routing\Route;

class NoValidator implements ValidatorInterface
{
    /**
     * Validator to match.
     *
     * @var Core\Routing\Validators\ValidatorInterface
     */
    private $next;

    /**
     * Check route against uri regex validator.
     *
     *
     * @param Core\Routing\Route $route Route to check
     *
     * @throws Core\Exceptions\Routing\InvalidRoutePrefix
     *
     * @return void|Core\Routing\Validators\ValidatorInterface
     */
    public function matches(Route $route)
    {
        return $route;
    }

    /**
     * Set the next validator to call once this has passed.
     *
     * @param Core\Routing\Validators\ValidatorInterface $validator
     *
     * @return void
     */
    public function setNext(ValidatorInterface $validator)
    {
    }

    /**
     * Call the next validator and execute the matches method.
     *
     * @param Core\Routing\Route
     *
     * @return Core\Routing\Validators\ValidatorInterface
     */
    public function callNext(Route $route)
    {
    }
}
