<?php

namespace App;

use App\Magic as Magic;
use App\Consumer as Consumer;
use Illuminate\Database\Eloquent\Model;

class ConsumptionRecord extends Model
{
    protected $fillable = [
      'consumer_id' , 'magic_id', 'amount', 'magic_name', 'level'
    ];

    function consumer(){
        return $this->belongsTo(Consumer::class)->select(array('id', 'name','money'));
    }
    public function magics(){
        return $this->belongsTo(Magic::class);
    }

}
