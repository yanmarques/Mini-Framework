<?php

namespace Core\Routing\Validators;

use Core\Routing\Validators\ValidatorInterface;
use Core\Exceptions\Routing\InvalidRouteAction;
use Core\Routing\Route;

class ActionValidator implements ValidatorInterface
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
     * @throws Core\Exceptions\Routing\InvalidRouteAction
     *
     * @param Core\Routing\Route $route Route to check
     * @return void|Core\Routing\Validators\ValidatorInterface
     */
    public function matches(Route $route)
    {
        // Action is a closure
        if ( is_callable($route->action()) || preg_match('/[a-zA-Z]+\@[a-zA-Z]+/', $route->action())) {
            return $this->callNext($route);
        }

        throw new InvalidRouteAction("The action [{$route->getRouteAction()}] is not valid");
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
