<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Support\Hash;
use Core\Views\View;

class HomeController
{
    public function index(Request $request)
    {
        $x = encrypt('mypassword');
        dd($x, decrypt($x));
        return view('home');
    }

    public function dashboard(Request $request)
    {
        return view('dashboard');
    }
}
