<?php

namespace App\Http\Controllers;

use APIReturn;
use App\Level;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    /**
     * 查询 Level 信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function info(Request $request)
    {
        try {
            $levelInfo = Level::where('level_id', $request->input('levelId'))->with('challenges')->first();
            return \APIReturn::success($levelInfo);
        } catch (\Exception $e) {
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 修改 Level 名
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function setName(Request $request)
    {
        $validator = Validator::make($request->only(['levelId', 'levelName']), [
            'levelId' => 'required|integer',
            'levelName' => 'required'
        ], [
            'levelId.required' => '缺少 Level ID 字段',
            'levelId.integer' => 'Level ID 字段不合法',
            'levelName.required' => '缺少 Level名 字段'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $level = Level::find($request->input('levelId'));
            $level->level_name = $request->input('levelName');
            $level->save();
            return \APIReturn::success($level);
        } catch (\Exception $e) {
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 修改发布时间
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function setReleaseTime(Request $request)
    {
        $validator = Validator::make($request->only(['levelId', 'releaseTime']), [
            'levelId' => 'required|integer',
            'releaseTime' => 'required|date'
        ], [
            'levelId.required' => '缺少 Level ID 字段',
            'levelId.integer' => 'Level ID 字段不合法',
            'releaseTime.required' => '缺少 发布时间 字段',
            'releaseTime.date' => '发布时间字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $level = Level::find($request->input('levelId'));
            $level->release_time = $request->input('releaseTime');
            $level->save();
            return \APIReturn::success($level);
        } catch (\Exception $e) {
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 修改开放规则
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function setRules(Request $request)
    {
        $validator = Validator::make($request->only(['levelId', 'rules']), [
            'levelId' => 'required|integer',
            'rules' => 'required|json'
        ], [
            'levelId.required' => '缺少 Level ID 字段',
            'levelId.integer' => 'Level ID 字段不合法',
            'rules.required' => '缺少 Rules 字段',
            'rules.json' => 'Rules 字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $level = Level::find($request->input('levelId'));
            $level->rules = json_decode($request->input('rules'), true);
            $level->save();
            return \APIReturn::success($level);
        } catch (\Exception $e) {
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 删除 Level
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function deleteLevel(Request $request)
    {
        $validator = Validator::make($request->only('levelId'), [
            'levelId' => 'required',
        ], [
            'levelId.required' => '缺少 Level ID 字段'
        ]);

        try{
            $level = Level::find($request->input('levelId'));

            if (!$level){
                return APIReturn::error("level_not_found", "Level 不存在", 404);
            }

            if ($level->challenges->count() > 0){
                return APIReturn::error("level_not_empty", "Level 下仍有 Challenge", 403);
            }

            $level->delete();
            return APIReturn::success();
        }
        catch (\Exception $e){
            return \APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
