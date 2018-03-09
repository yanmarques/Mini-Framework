<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use Core\Support\Hash;
use App\Models\User;

class AuthController
{
    public function login()
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        $user = User::store($request->all());
        session()->set('user', $user->toArray());
        return redirect('/');
    }
}
