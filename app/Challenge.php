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
}
