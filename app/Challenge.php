<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $table = "challenges";
    protected $primaryKey = "challenge_id";
    protected $casts = [
        'config' => 'array',
        'is_dynamic_flag' => 'boolean',
        'score' => 'float'
    ];

    public function getReleaseTimeAttribute($value){
        return Carbon::parse($value, 'UTC')->toIso8601String();
    }

    public function flags()
    {
        return $this->hasMany('App\Flag', 'challenge_id', 'challenge_id');
    }

    public function logs()
    {
        return $this->hasMany('App\Log', 'challenge_id', 'challenge_id');
    }

    public function level(){
        return $this->belongsTo('App\Level', 'level_id', 'level_id');
    }
}
