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
        // Salva usuario

        return redirect('/');
    }
}
