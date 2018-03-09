<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Support\Hash;
use Core\Views\View;

class HomeController
{
    public function index(Request $request)
    {
        $e = ['s'];
        $e[2];
        return view('home');
    }
}
