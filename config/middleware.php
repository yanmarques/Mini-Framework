<?php

return [

    /*
    |********************************************************************
    | Register Global HTTP Middlewares
    |********************************************************************
    |
    | Here you can register your global Middlewares to use on all your
    | requets. Middlewares are decorators to execute.
    |
    */
    'global' => [
        Core\Http\Middleware\NullableStringMiddleware::class,
        Core\Http\Middleware\CSRFMiddleware::class,
    ],

    /*
    |********************************************************************
    | Register HTTP Middlewares
    |********************************************************************
    |
    | Here you can register all your Middlewares to use on routes middleware.
    |
    */
    'web' => [
        //
    ],

];
