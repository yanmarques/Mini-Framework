<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Support\Hash;

class AuthController
{
    public function login()
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        // Make your login logic...

        return redirect('/');
    }
}
