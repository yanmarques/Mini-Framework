<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Views\View;

class HomeController
{
    public function index(Request $request)
    {
        return view('home');
    }
}
