<?php

return [

    /*
    |********************************************************************
    | Database Driver
    |********************************************************************
    |
    | Here you can configure the database driver.
    | Database drivers are the database technology that you will use for
    | your project.
    |
    | Drivers: mysql, pgsql
    |
    | Please check your database driver documentation before configurating.
    |
    */
    'driver' => 'mysql',

    /*
    |********************************************************************
    | Database Name
    |********************************************************************
    |
    | Here you can configure the database name to connect the socket.
    |
    | Please check your database driver documentation before configurating.
    |
    */
    'dbname' => 'test',

    /*
    |********************************************************************
    | Database Host
    |********************************************************************
    |
    | Here you can configure the host where database is running.
    |
    | Please check your database driver documentation before configurating.
    |
    */
    'host' => '127.0.0.1',

    /*
    |********************************************************************
    | Database Port
    |********************************************************************
    |
    | Here you can configure the port where database is being served.
    |
    | Defaults: pgsql=5432;mysql=3306
    |
    | Please check your database driver documentation before configurating.
    |
    */
    'port' => '3306',

    /*
    |********************************************************************
    | Database User
    |********************************************************************
    |
    | Here you can configure database connection. In way to connect to
    | database, you must pass a username.
    |
    | Please check your database driver documentation before configurating.
    |
    */
    'user' => 'root',

    /*
    |********************************************************************
    | Database Password
    |********************************************************************
    |
    | Here you can configure database connection. In way to connect to
    | database, you must pass a password.
    |
    | Please check your database driver documentation before configurating.
    |
    */
    'password' => 'changeme',

    /*
    |********************************************************************
    | Phinx Path
    |********************************************************************
    |
    | Here you set phinx paths configuration. Phinx is a powerfull easy way to
    | handle database migrations.
    |
    | See https://book.cakephp.org/3.0/en/phinx.html for phinx project.
    | See https://book.cakephp.org/3.0/en/phinx/configuration.html for
    | configuration details.
    |
    */
    'paths' => [

        /*
        |********************************************************************
        | Migrations Path
        |********************************************************************
        |
        | Here you set the path to migration files. Phinx will create custom
        | migration files on this path. With phinx you can create tables,
        | columns with some line of code.
        |
        */
        'migrations' => 'database/migrations',

        /*
        |********************************************************************
        | Seeds Path
        |********************************************************************
        |
        | Here you set the path to seeds files. Seeds are used to create fake
        | data to your database for development purposes.
        |
        */
        'seeds' => 'database/seeds',
    ],
];
