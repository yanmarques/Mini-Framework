<?php

namespace App\Models;

use Core\Database\Model;

class User extends Model
{
    /**
     * The attributes to mass assignment
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'password',
        'email'
    ];

    protected static function boot()
    {
        static::creating(function (User $user) {
            $user->password = hcrypt($user->password);
        });
    }
}