<?php

namespace App\Http\Controllers;

use Core\Http\Request;

class AuthController
{
    public function login()
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        // Make your login logic...
        // Authenticate user

        return redirect('/');
    }
}
