<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $table = "challenges";
    protected $primaryKey = "challenge_id";
    protected $casts = [
        'config' => 'array'
    ];

    public function flags(){
        return $this->hasMany('App\Flag', 'challenge_id', 'challenge_id');
    }
}
