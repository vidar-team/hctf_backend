<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $primaryKey = "level_id";
    protected $casts = [
      'rules' => 'array'
    ];

    public function category(){
        return $this->belongsTo('App\Category', 'category_id', 'category_id');
    }

    public function challenges(){
        return $this->hasMany('App\Challenge', 'level_id', 'level_id');
    }

    public function logs(){
        return $this->hasMany('App\Log', "level_id", "level_id");
    }
}
