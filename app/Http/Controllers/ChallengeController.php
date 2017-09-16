<?php

namespace App\Http\Controllers;

use APIReturn;
use App\Challenge;
use Illuminate\Http\Request;
use Validator;

class ChallengeController extends Controller
{
    /**
     * 创建新的 Challenge
     *
     * 权限要求: ['isLogin', 'isAdmin']
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'url' => 'required|url',
            'score' => 'required|numeric',
            'levelId' => 'required|integer',
            'flag' => 'required|array',
            'release_time' => 'required|date'
        ], [
            'title.required' => '缺少标题字段',
            'description.required' => '缺少说明字段',
            'url.required' => '缺少 url 字段',
            'url.url' => 'url 字段不合法',
            'score.required' => '缺少基础分数字段',
            'score.numeric' => '基础分数字段不合法',
            'levelId.required' => '缺少 Level ID 字段',
            'levelId.integer' => 'Level ID 字段不合法',
            'flag.required' => '缺少 Flag 字段',
            'flag.array' => 'Flag 字段不合法',
            'releaseTime.required' => '缺少发布时间字段',
            'releaseTime.date' => '发布时间字段不合法'
        ]);

        if ($validator->fails()){
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            $newChallenge = new Challenge();

            $newChallenge->title = $request->input('title');
            $newChallenge->description = $request->input('description');
            $newChallenge->url = $request->input('url');
            $newChallenge->score = $request->input('score');
            $newChallenge->release_time = $request->input('release_time');

            $newChallenge->save();

            return APIReturn::success([
               "challenge" => $newChallenge
            ]);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 查询指定ID Challenge 信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function info(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ], [
            'id.required' => '缺少Challenge ID字段',
            'id.integer' => 'Challenge ID不合法'
        ]);

        if ($validator->fails()){
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }
        try{
            $question = Challenge::find($request->input('id'));
            return APIReturn::success([
                'challenge' => $question
            ]);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
