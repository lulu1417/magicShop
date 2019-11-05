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
    function getID($id)
    {
        $ID = $this->where('id', $id)->first()->id;
        return $ID;
    }

    function getPrice($id)
    {
        $price = $this->where('id', $id)->first()->price;
        return $price;
    }

    function getMagicName($id)
    {
        $name = $this->where('id', $id)->first()->magic_name;
        return $name;
    }

    function getLevel($id)
    {
        $level = $this->where('id', $id)->first()->level;
        return $level;
    }

}
