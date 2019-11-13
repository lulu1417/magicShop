<?php

namespace App;


use App\ConsumptionRecord as Record;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Consumer extends Authenticatable
{
    protected $fillable = [
        'name', 'password', 'api_token', 'money', 'password', 'api_token'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    function getName($id)
    {
        $name = $this->find($id)->name;
        return $name;
    }

    function getPassword($name)
    {
        $password = $this->where('name', $name)->first()->password;
        return $password;
    }

    function getConsumer($name)
    {
        $consumer = $this->where('name', $name)->first();
        return $consumer;
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    function getMoney($consumer)
    {
        if(gettype($consumer)==('integer')){
            $money = $this->find($consumer)->money;
            return $money;
        }elseif(gettype($consumer)==('string')){
            $money = $this->where('name', $consumer)->first()->money;
            return $money;
        }

    }

}
