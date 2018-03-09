<?php
    return [
        /*
        |********************************************************************
        | Session Driver
        |********************************************************************
        |
        | Mini provides you a list of drivers to make use as sessions manager.
        | This option defines the driver to controll the sessions on requests.
        |
        | Drivers: file, cookie
        |
        */
        'session_driver' => 'file',

        /*
        |********************************************************************
        | Session Lifetime
        |********************************************************************
        |
        | This option defines your session lifetime. Expired sessions will
        | not be considered anymore. This option can vary on your application
        | purposes. The value will be in minutes.
        |
        */
        'session_lifetime' => 120,

        /*
        |********************************************************************
        | Session Encrypt
        |********************************************************************
        |
        | This option indicate wheter you want to secure store your sessions
        | using built-in encryptation. You will be able to use sessions like 
        | normal, but it will use symmetric encryptation, and will only be 
        | decrypted on server.
        |
        */
        'encrypt' => true,

        /*
        |********************************************************************
        | Session Path
        |********************************************************************
        |
        | This option defines the path to save sessions when on file driver.
        |
        */
        'path' => 'sessions',
    ];