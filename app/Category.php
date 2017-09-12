<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";
    protected $primaryKey = "category_id";

    public function levels(){
        return $this->hasMany('App\Level', 'category_id', 'category_id');
    }

    public function challenges(){
        return $this->hasManyThrough(
            'App\Challenge',
            'App\Level',
            'category_id',
            'level_id',
            'category_id',
            'level_id'
        );
    }
}
