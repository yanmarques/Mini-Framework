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
        dd($request->all());
        return redirect()->toView('home', [
            'name' => $request->name,
            'password' => hcrypt($request->password)
        ]);
    }
}
