<?php

namespace App;


use App\ConsumptionRecord as Record;
use Illuminate\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Consumer extends Authenticatable
{
    protected $fillable = [
        'name', 'password', 'api_token', 'money',
    ];
//    protected $hidden = [
//
//    ];
    public function record()
    {
        return $this->hasMany(Record::class);
    }
    function getName(){
        //
    }
    function getToken(){
        //
    }

    protected function getBalance(){
        //
    }

}
