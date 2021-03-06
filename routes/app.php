<?php

namespace Routes;

/*
|---------------------------------------------------------------
| Routes
|---------------------------------------------------------------
| Here you are able to register all your application routes.
| You can choose between these methods:
| GET, POST, PUT, PATCH, DELETE
|
| Then you call the variable $route and access the function
| as the method in lowercase
|
*/

$route->middleware(['auth'], function ($route) {
    $route->get('/users', 'AuthController@user');
});

$route->get('/', 'HomeController@index');
$route->get('/login', 'AuthController@login');
$route->post('/auth', 'AuthController@auth');

$route->get('/user', 'UserController@index');
