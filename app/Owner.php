<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Owner extends Authenticatable
{
    protected $fillable = [
        'name','password', 'api_token',
    ];

//    function getName(){
//        //
//    }
//    function getToken(){
//        //
//    }

}
