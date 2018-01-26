<?php

namespace App\Http\Controllers;

use Core\Http\Request;
use App\Models\User;

class UserController
{
    public function index()
    {
        dd(User::all());
    }
}
