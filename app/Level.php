<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $primaryKey = "level_id";
    protected $dates = [
        'created_at',
        'updated_at',
        'release_time'
    ];
    protected $casts = [
        'rules' => 'array'
    ];

    public function getReleaseTimeAttribute($value){
        return Carbon::parse($value, 'UTC')->toIso8601String();
    }

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id', 'category_id');
    }

    public function challenges()
    {
        return $this->hasMany('App\Challenge', 'level_id', 'level_id');
    }

    public function logs()
    {
        return $this->hasMany('App\Log', "level_id", "level_id");
    }
}
