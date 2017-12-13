<?php

return [

    /*
    |********************************************************************
    | Register Application Services
    |********************************************************************
    |
    | Here you can register all the application services. Services are
    | components to resolve critic configurations on your application.
    |
    | Please DO NOT change this configuration unless you know exactly
    | what you are doing
    |
    */
    Core\Services\RoutingService::class,
    Core\Services\SessionsService::class,
    Core\Services\CrypterService::class,
    Core\Services\ConfigService::class,
    Core\Services\ViewService::class
];
