<?php

namespace App\Http\Controllers;

use APIReturn;
use App\Question;
use Illuminate\Http\Request;
use Validator;

class QuestionController extends Controller
{
    /**
     * 创建新的 Question
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
            'release_time' => 'required|date'
        ], [
            'title.required' => '缺少标题字段',
            'description.required' => '缺少说明字段',
            'url.required' => '缺少 url 字段',
            'url.url' => 'url 字段不合法',
            'score.required' => '缺少基础分数字段',
            'score.numeric' => '基础分数字段不合法',
            'release_time.required' => '缺少发布时间字段',
            'release_time.date' => '发布时间字段不合法'
        ]);

        if ($validator->fails()){
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            $newQuestion = new Question();

            $newQuestion->title = $request->input('title');
            $newQuestion->description = $request->input('description');
            $newQuestion->url = $request->input('url');
            $newQuestion->score = $request->input('score');
            $newQuestion->release_time = $request->input('release_time');

            $newQuestion->save();

            return APIReturn::success([
               "question" => $newQuestion
            ]);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 查询指定ID Question 信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function info(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ], [
            'id.required' => '缺少问题ID字段',
            'id.integer' => '问题ID不合法'
        ]);

        if ($validator->fails()){
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }
        try{
            $question = Question::find($request->input('id'));
            return APIReturn::success([
                'question' => $question
            ]);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
