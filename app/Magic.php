<?php

namespace App;


use App\ConsumptionRecord as Record;
use Illuminate\Database\Eloquent\Model;

class Magic extends Model
{
    protected $fillable = [
        'magic_name', 'level', 'price'
    ];

    public function record()
    {
        return $this->hasMany('Record');
    }

    function getPrice()
    {
        return $this->price;
    }

    function getMagicName()
    {
        return $this->magic_name;
    }

    function getLevel()
    {
        return $this->level;
    }

}
