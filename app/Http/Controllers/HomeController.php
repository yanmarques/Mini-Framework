<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Support\Hash;
use Core\Views\View;

class HomeController
{
    public function index(Request $request)
    {
        return view('home');
    }

    public function dashboard(Request $request)
    {
        return view('dashboard');
    }
}
