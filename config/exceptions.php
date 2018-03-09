<?php

return [
    /*
    |********************************************************************
    | Catcher
    |********************************************************************
    |
    | Configure the path for the catcher exception handler. Catcher will
    | let you able to handle php errors and application exceptions, making 
    | redirects or debugging it on development environment with pretty
    | Whoops handler.
    |
    */
    'catcher' => \App\Exceptions\Catcher::class,

    /*
    |********************************************************************
    | Views
    |********************************************************************
    |
    | Path to exception views to be handled when on production environment.
    |
    */
    'views' => __DIR__ . '/app/Exceptions/views',

    /*
    |********************************************************************
    | Whoops Handler
    |********************************************************************
    |
    | Here you can configure the default debug handler to be rendered on 
    | exceptions running on development environment.
    |
    | Supported: PrettyPageHandler, PlainTextHandler, CallbackHandler, 
    | JsonResponseHandler, XmlResponseHandler.
    |
    | @see https://github.com/filp/whoops
    */
    'whoops' => 'PrettyPageHandler'
];