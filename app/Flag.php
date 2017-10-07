<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flag extends Model
{
    protected $table = "flags";
    protected $primaryKey = "flag_id";
    protected $fillable = ['flag', 'team_id'];

    public function level()
    {
        return $this->belongsTo('App\Level', 'level_id', 'level_id');
    }

    public function challenge()
    {
        return $this->belongsTo('App\Challenge', 'challenge_id', 'challenge_id');
    }
}
