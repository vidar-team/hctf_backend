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
    | id             | int(10) unsigned | NO     | PRI   | <null>    | auto_increment |
    | teamName       | varchar(255)     | NO     |       | <null>    |                |
    | email          | varchar(255)     | YES    | UNI   | <null>    |                |
    | password       | varchar(255)     | NO     |       | <null>    |                |
    | signUpTime     | datetime         | NO     |       | <null>    |                |
    | lastLoginTime  | datetime         | NO     |       | <null>    |                |
    | score          | decimal(8,2)     | NO     |       | 0.00      |                |
    | banned         | tinyint(1)       | NO     |       | 0         |                |
    | remember_token | varchar(100)     | YES    |       | <null>    |                |
     *
     */
    protected $fillable = [
        'teamName', 'email', 'password', 'signUpTime', 'lastLoginTime'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $timestamps = false;
}