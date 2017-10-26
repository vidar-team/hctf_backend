<?php
namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

/**
 * 检查是否在比赛时间范围内
 * Class TimeCheck
 * @package App\Http\Middleware
 * @author Eridanus Sora <sora@sound.moe>
 */
class TimeCheck {
    public function handle($request, Closure $next){
        $config = collect(\DB::table("config")->get())->pluck('value', 'key');
        $start = Carbon::parse($config["start_time"], 'UTC');
        $end = Carbon::parse($config["end_time"], 'UTC');
        $now = Carbon::now("Asia/Shanghai");
        if ($now->lt($start) || $now->gt($end)){
            return \APIReturn::error("under_maintenance", [
                $start->toIso8601String(),
                $end->toIso8601String()
            ]);
        }
        else{
            return $next($request);
        }
    }
}