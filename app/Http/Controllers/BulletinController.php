<?php

namespace App\Http\Controllers;

use APIReturn;
use App\Bulletin;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    /**
     * 获得全部公告
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function list(Request $request)
    {
        try {
            return APIReturn::success(Bulletin::orderBy('bulletin_id', 'desc')->get());
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 创建公告
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function create(Request $request)
    {
        $validator = \Validator::make($request->only(['title', 'content']), [
            'title' => 'required',
            'content' => 'required'
        ], [
            'title.required' => '缺少标题字段',
            'content.required' => '缺少内容字段'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $bulletin = new Bulletin();
            $bulletin->title = $request->input('title');
            $bulletin->content = $request->input('content');
            $bulletin->save();
            return APIReturn::success($bulletin);
        } catch (\Exception $e) {
            dump($e);
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 删除公告
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function delete(Request $request)
    {
        $validator = \Validator::make($request->only(['bulletinId']), [
            'bulletinId' => 'required'
        ], [
            'bulletinId.required' => '缺少公告ID字段'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            $bulletin = Bulletin::find($request->input('bulletinId'));
            if (!$bulletin){
                return APIReturn::error("bulletin_not_found", "该公告不存在", 404);
            }
            $bulletin->delete();
            return APIReturn::success();
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 修改公告
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function edit(Request $request){
        $validator = \Validator::make($request->only(['bulletinId', 'title', 'content']), [
           'bulletinId' => 'required',
           'title' => 'required',
           'content' => 'required'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            $bulletin = Bulletin::find($request->input('bulletinId'));
            if (!$bulletin){
                return APIReturn::error("bulletin_not_found", "该公告不存在", 404);
            }
            $bulletin->title = $request->input('title');
            $bulletin->content = $request->input('content');
            $bulletin->save();
            return APIReturn::success($bulletin);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
