<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Team extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * | team_id             | int(10) unsigned | NO     | PRI   | <null>    | auto_increment |
     * | team_name       | varchar(255)     | NO     |       | <null>    |                |
     * | email          | varchar(255)     | YES    | UNI   | <null>    |                |
     * | password       | varchar(255)     | NO     |       | <null>    |                |
     * | signUpTime     | datetime         | NO     |       | <null>    |                |
     * | lastLoginTime  | datetime         | NO     |       | <null>    |                |
     * | score          | decimal(8,2)     | NO     |       | 0.00      |                |
     * | banned         | tinyint(1)       | NO     |       | 0         |                |
     * | remember_token | varchar(100)     | YES    |       | <null>    |                |
     *
     */
    protected $fillable = [
        'team_name', 'email', 'password', 'signUpTime', 'lastLoginTime'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $primaryKey = "team_id";

    protected $casts = [
        'admin' => 'boolean',
        'banned' => 'boolean'
    ];

    public $timestamps = false;

    public function logs(){
        return $this->hasMany("App\Log", "team_id", "team_id");
    }

    /**
     * 分数和
     * @return float
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function getScoreAttribute(){
        return floatval($this->logs()->sum('score'));
    }
}