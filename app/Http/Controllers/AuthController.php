<?php

namespace App\Http\Controllers;

use Core\Http\Request;

class AuthController
{
    public function login(Request $request)
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        return redirect('/dashboard', ['message' => 'Voce esta cadastrado']);
    }
}
