<?php

namespace App;

use Carbon\Carbon;
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
     * | hduer          | tinyint(1)       | NO     |       | 0         |                |
     * | student_id     | varchar(255)     | YES    | UNI   | <null>    |                |
     * | real_name      | varchar(255)     | YES    |       | <null>    |                |
     * | college        | varchar(255)     | YES    |       | <null>    |                |
     *
     */
    protected $fillable = [
        'team_name', 'email', 'password', 'signUpTime', 'lastLoginTime', 'token', 'hduer', 'student_id', 'real_name', 'college'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $primaryKey = "team_id";

    protected $casts = [
        'admin' => 'boolean',
        'banned' => 'boolean',
        'hduer' => 'boolean',
        'dynamic_total_score' => 'float'
    ];

    public $timestamps = false;

    public function logs()
    {
        return $this->hasMany("App\Log", "team_id", "team_id");
    }

    /**
     * 分数和
     * @return float
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function getScoreAttribute()
    {
        return floatval($this->logs()->sum('score'));
    }

    /**
     * 按分数排行
     * @param $query
     * @param string $order
     * @return mixed
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function scopeOrderByScore($query, $order = "desc")
    {
        return $query->leftJoin('logs', function ($join) {
            $join->on('logs.team_id', '=', 'teams.team_id')->where('status', '=', 'correct');
        })
            ->groupBy(['teams.team_id'])
            ->addSelect(['*', \DB::raw('sum(logs.score) as dynamic_total_score')])
            ->orderBy('dynamic_total_score', $order);
    }

    /**
     * 周榜分数排序(hgame专属)
     * @param $query
     * @param string $order
     * @return mixed
     * @author hammer
     */
    public function scopeOrderByScoreForWeek($query, $order = "desc")
    {
        return $query->leftJoin('logs', function ($join) {
            $startTime = collect(\DB::table("config")->get())->pluck('value', 'key')["start_time"];
            $startTime = Carbon::parse($startTime);
            $weeks = Carbon::now()->diffInWeeks($startTime) + 1;
            $afterTheWeek = $startTime->addWeeks($weeks)->toDateTimeString();
            $join->on('logs.team_id', '=', 'teams.team_id')->where([
                ['status', '=', 'correct'],
                ['logs.created_at', '<', $afterTheWeek],
            ]);
        })
            ->groupBy(['teams.team_id'])
            ->addSelect(['*', \DB::raw('sum(logs.score) as dynamic_total_score')])
            ->orderBy('dynamic_total_score', $order);
    }
}
