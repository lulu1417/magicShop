<?php

namespace App;

use App\Magic as Magic;
use App\Consumer as Consumer;
use Illuminate\Database\Eloquent\Model;

class ConsumptionRecord extends Model
{
    protected $fillable = [
      'consumer_id' , 'magic_id',
    ];

    public function consumer(){
        return $this->belongsTo(Consumer::class);
    }
    public function magic(){
        return $this->belongsTo(Magic::class);
    }

}
