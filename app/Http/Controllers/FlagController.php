<?php

namespace App\Http\Controllers;

use APIReturn;
use App\Flag;
use Illuminate\Http\Request;

class FlagController extends Controller
{
    /**
     * 删除 Flag
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function deleteFlag(Request $request)
    {
        $validator = \Validator::make($request->only(['flagId']), [
            'flagId' => 'required|integer'
        ], [
            'flagId.required' => '缺少 Flag ID 字段',
            'flagId.integer' => 'Flag ID 字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $flag = Flag::find($request->input('flagId'));
            $flag->delete();
            return APIReturn::success();
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 编辑 Flag
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function editFlag(Request $request)
    {
        $validator = \Validator::make($request->only(['flagId', 'flag', 'teamId']), [
            'flagId' => 'required|integer',
            'flag' => 'required',
            'teamId' => 'required'
        ], [
            'flagId.required' => '缺少 Flag ID 字段',
            'flagId.integer' => 'Flag ID 字段不合法',
            'flag.required' => '缺少 Flag 字段',
            'teamId.required' => '缺少 Team ID 字段'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $flag = Flag::find($request->input('flagId'));

            if (!$flag) {
                return APIReturn::error('flag_not_found', 'Flag 不存在', 404);
            }

            $flag->flag = $request->input('flag');
            $flag->team_id = $request->input('teamId');
            $flag->save();

            return APIReturn::success($flag);
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
