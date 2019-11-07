<?php

namespace App;


use App\ConsumptionRecord as Record;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Consumer extends Authenticatable
{
    protected $fillable = [
        'name', 'password', 'api_token', 'money',
    ];
    protected $hidden = [
        'password', 'api_token'
    ];

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    function getName($id)
    {
        $name = $this->find($id)->name;
        return $name;
    }

    function getMoney($id)
    {
        $money = $this->find($id)->money;
        return $money;
    }

}
