<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class Owner extends Authenticatable
{
    protected $fillable = [
        'name','password', 'api_token',
    ];
    function getPassword($name)
    {
        $password = $this->where('name', $name)->first()->password;
        return $password;
    }
    function getOwner($name)
    {
        $owner = $this->where('name', $name)->first();
        return $owner;
    }

}
