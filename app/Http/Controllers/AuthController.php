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
        return redirect()->toView('home', [
            'name' => $request->name,
            'password' => hcrypt($request->password)
        ])->status(201);
    }
}
