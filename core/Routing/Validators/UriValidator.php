<?php

namespace Core\Routing\Validators;

use Core\Routing\Validators\ValidatorInterface;
use Core\Exceptions\Routing\InvalidRoutePrefix;
use Core\Routing\Route;

class UriValidator implements ValidatorInterface
{
    /**
     * Validator to match
     *
     * @var Core\Routing\Validators\ValidatorInterface
     */
    private $next;

    /**
     * Check route against uri regex validator
     *
     * @throws Core\Exceptions\Routing\InvalidRoutePrefix
     *
     * @param Core\Routing\Route $route Route to check
     * @return void|Core\Routing\Validators\ValidatorInterface
     */
    public function matches(Route $route)
    {
        // Use regex to validate url prefix
        if ( preg_match('/\/?([a-zA-Z_-]+\/)*/', $route->uri()) ) {
            return $this->callNext($route);
        }

        throw new InvalidRoutePrefix("The prefix [{$route->uri()}] is not valid");
    }

    /**
     * Set the next validator to call once this has passed
     *
     * @param Core\Routing\Validators\ValidatorInterface $validator
     * @return void
     */
    public function setNext(ValidatorInterface $validator)
    {
        $this->next = $validator;
    }

    /**
     * Call the next validator and execute the matches method
     *
     * @param Core\Routing\Route
     * @return Core\Routing\Validators\ValidatorInterface
     */
    public function callNext(Route $route)
    {
        return $this->next->matches($route);
    }
}
