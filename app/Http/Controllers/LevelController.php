<?php

namespace App\Http\Controllers;

use App\Level;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    /**
     * 查询 Level 信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function info(Request $request){
        try{
            $levelInfo = Level::find($request->input('levelId'));
            return \APIReturn::success($levelInfo);
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 修改 Level 名
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function setName(Request $request){
        $validator = \Validator::make($request->only(['levelId', 'levelName']), [
            'levelId' => 'required|integer',
            'levelName' => 'required'
        ], [
            'levelId.required' => '缺少 Level ID 字段',
            'levelId.integer' => 'Level ID 字段不合法',
            'levelName.required' => '缺少 Level名 字段'
        ]);
        try{
            $level = Level::find($request->input('levelId'));
            $level->level_name = $request->input('levelName');
            $level->save();
            return \APIReturn::success($level);
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 修改发布时间
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function setReleaseTime(Request $request){
        $validator = \Validator::make($request->only(['levelId', 'releaseTime']), [
            'levelId' => 'required|integer',
            'releaseTime' => 'required'
        ], [
            'levelId.required' => '缺少 Level ID 字段',
            'levelId.integer' => 'Level ID 字段不合法',
            'releaseTime.required' => '缺少 Level名 字段'
        ]);
        try{
            $level = Level::find($request->input('levelId'));
            $level->release_time = $request->input('releaseTime');
            $level->save();
            return \APIReturn::success($level);
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
