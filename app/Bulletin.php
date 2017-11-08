<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    protected $table = "bulletin";
    protected $primaryKey = "bulletin_id";
    public $timestamps = false;
}
