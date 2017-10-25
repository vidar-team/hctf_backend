<?php

namespace App\Http\Controllers;

use APIReturn;
use Carbon\Carbon;
use Config;
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
        return \APIReturn::success([
            "startTime" => Carbon::parse(Config::get("ctf.startTime"))->toIso8601String(),
            "endTime" => Carbon::parse(Config::get("ctf.endTime"))->toIso8601String(),
            "flagPrefix" => Config::get("ctf.flagPrefix"),
            "flagSuffix" => Config::get("ctf.flagSuffix")
        ]);
    }

    public function editMetaInfo(Request $request){
        $validator = \Validator::make($request->only(['startTime', 'endTime', 'flagPrefix', 'flagSuffix']), [
           'startTime' => 'required|date',
           'endTime' => 'required|date',
           'flagPrefix' => 'required',
           'flagSuffix' => 'required'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        Config::set('ctf.startTime', $request->input('startTime'));
        Config::set('ctf.endTime', $request->input('endTime'));
    }
}