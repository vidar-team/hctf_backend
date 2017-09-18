<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $primaryKey = "log_id";
    protected $table = "logs";

    public function level(){
        return $this->belongsTo('App\Level', 'level_id', 'level_id');
    }
}
