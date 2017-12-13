<?php

/*
|********************************************************************
| Register Composer Autoloader
|********************************************************************
|
| Here the autoload from composer is required to resolve namespaces,
| classes and files through composer.json configurations
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|********************************************************************
| Putting to Up and Running
|********************************************************************
|
| Here it is where everything begins. The application kernel will be
| loaded, services, and configurations will be setted up. Once it is
| done, the kernel will be able to handle an incoming request and
| respond correctly to it. Enjoy!
|
*/

$kernel = new Core\Bootstrapers\Application(
    dirname(__DIR__)
);

$response = $kernel->handle(
    Core\Http\Request::get()
);

$response->send();
