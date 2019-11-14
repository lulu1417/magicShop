<?php

namespace App;


use App\ConsumptionRecord as Record;
use Illuminate\Database\Eloquent\Model;

class Magic extends Model
{
    protected $fillable = [
        'magic_name', 'level', 'price', 'photo'
    ];

    public function record()
    {
        return $this->hasMany('Record');
    }
    function getID($id)
    {
        $ID = $this->find($id)->id;
        return $ID;
    }

    function getPrice($id)
    {
        $price = $this->find($id)->price;
        return $price;
    }

    function getMagicName($id)
    {
        $name = $this->find($id)->magic_name;
        return $name;
    }

    function getLevel($id)
    {
        $level = $this->find($id)->level;
        return $level;
    }
    function getImage($id)
    {
        $photo = $this->find($id)->photo;
        return $photo;
    }

}
