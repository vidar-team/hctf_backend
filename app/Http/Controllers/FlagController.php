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
}
