<?php

return [
    
    /*
    |********************************************************************
    | Database
    |********************************************************************
    |
    | Here you can configure the database and set database driver.
    | Database drivers supported for now is only Postgresql.
    |
    | Drivers: pgsql
    |
    */
    'driver' => 'pgsql',

    /*
    |********************************************************************
    | Configurations
    |********************************************************************
    |
    | Configure your database connection parameters. You need database
    | host, the port, username e password. If you got confused with that,
    | please check Postgresql documentation here:
    | https://www.postgresql.org/docs/9.6/static/runtime-config.html
    | 
    */
    'configurations' => [
        'dbname' => 'test',
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'password' => 'changeme'
    ]
];