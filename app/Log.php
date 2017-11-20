<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $primaryKey = "log_id";
    protected $table = "logs";

    public function level()
    {
        return $this->belongsTo('App\Level', 'level_id', 'level_id');
    }

    public function team(){
        return $this->belongsTo('App\Team', 'team_id', 'team_id');
    }

    public function challenge(){
        return $this->belongsTo('App\Challenge', 'challenge_id', 'challenge_id');
    }
}
