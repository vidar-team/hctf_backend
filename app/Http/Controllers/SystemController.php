<?php

namespace App\Http\Controllers;

use APIReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * 获得系统设置信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function getMetaInfo(Request $request)
    {
        $config = collect(\DB::table("config")->get())->pluck('value', 'key');
        return APIReturn::success([
            'startTime' => Carbon::parse($config['start_time'], 'UTC')->toIso8601String(),
            'endTime' => Carbon::parse($config['end_time'], 'UTC')->toIso8601String(),
            'flagPrefix' => $config['flag_prefix'],
            'flagSuffix' => $config['flag_suffix'],
            'ctfPattern' => $config['ctf_pattern']
        ]);
    }

    /**
     * 编辑系统设置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function editMetaInfo(Request $request){
        $validator = \Validator::make($request->only(['startTime', 'endTime', 'flagPrefix', 'flagSuffix', 'ctfPattern']), [
           'startTime' => 'required|date',
           'endTime' => 'required|date',
           'flagPrefix' => 'required',
           'flagSuffix' => 'required',
            'ctfPattern' => array('required', 'regex:/^(hctf|hgame)$/i')
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            \DB::table("config")->where('key', '=', 'start_time')->update([
                'value' => Carbon::parse($request->input('startTime'))->setTimezone('UTC')->toDateTimeString()
            ]);
            \DB::table("config")->where('key', '=', 'end_time')->update([
                'value' => Carbon::parse($request->input('endTime'))->setTimezone('UTC')->toDateTimeString()
            ]);
            \DB::table("config")->where('key', '=', 'flag_prefix')->update([
                'value' => $request->input('flagPrefix')
            ]);
            \DB::table("config")->where('key', '=', 'flag_suffix')->update([
                'value' => $request->input('flagSuffix')
            ]);
            \DB::table("config")->where('key', '=', 'ctf_pattern')->update([
                'value' => $request->input('ctfPattern')
            ]);
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }

        return APIReturn::success();
    }
}