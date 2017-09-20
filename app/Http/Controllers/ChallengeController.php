<?php

namespace App\Http\Controllers;

use APIReturn;
use App\Category;
use App\Challenge;
use App\Flag;
use App\Level;
use App\Log;
use App\Services\RuleValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use JWTAuth;
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
            'config' => 'required|json',
            'releaseTime' => 'required|date'
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
            'config.required' => '缺少设置字段',
            'config.json' => '设置字段不合法',
            'releaseTime.required' => '缺少发布时间字段',
            'releaseTime.date' => '发布时间字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $newChallenge = new Challenge();

            $newChallenge->title = $request->input('title');
            $newChallenge->description = $request->input('description');
            $newChallenge->url = $request->input('url');
            $newChallenge->score = $request->input('score');
            $newChallenge->level_id = $request->input('levelId');
            $newChallenge->config = $request->input('config');
            $newChallenge->release_time = $request->input('releaseTime');

            $newChallenge->save();
            $newChallenge->flags()->createMany($request->input('flag'));

            return APIReturn::success([
                "challenge" => $newChallenge
            ]);
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 查询指定ID Challenge 信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ], [
            'id.required' => '缺少Challenge ID字段',
            'id.integer' => 'Challenge ID不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }
        try {
            $question = Challenge::find($request->input('id'));
            return APIReturn::success([
                'challenge' => $question
            ]);
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 获得可用题目
     * @param Request $request
     * @author Eridanus Sora <sora@sound.moe>
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $team = JWTAuth::parseToken()->toUser();
        $team->load(['logs' => function ($query) {
            $query->where('status', 'correct');
        }]);
        $categories = Category::with(["levels", 'challenges'])->get();

        $validLevels = collect([]);
        $result = collect([]);

        $categories->every(function ($category) use ($validLevels, $team) {
            collect($category->levels)->every(function ($level) use ($validLevels, $team) {
                if ((new RuleValidator($team->team_id, $level->rules))->check($team->logs)) {
                    $validLevels->push($level->level_id);
                }
            });
        });

        $categories->map(function ($category) use ($validLevels, $result) {
            $result[$category->category_name] = $category->challenges->filter(function ($challenge) use ($validLevels) {
                return $validLevels->contains($challenge->level_id);
            })->groupBy('level_id');
        });

        return APIReturn::success($result);
    }

    /**
     * 提交 Flag
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function submitFlag(Request $request)
    {
        $validator = Validator::make($request->only('flag'), [
            'flag' => 'required'
        ], [
            'flag.required' => '缺少 Flag 字段'
        ]);

        $team = JWTAuth::parseToken()->toUser();
        $team->load(['logs' => function ($query) {
            $query->where('status', 'correct');
        }]);


        if ($validator->fails()){
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }
        try{
            $flag = Flag::where('flag', $request->input('flag'))->first();

            if (!$flag){
                //  Flag 不正确
                return APIReturn::error("wrong_flag", "Flag 不正确", 403);
            }

            $level = Level::find($flag->challenge->level_id);

            if (Log::where([
                'challenge_id' => $flag->challenge_id,
                'status' => 'correct'
            ])->first()){
                return APIReturn::error("duplicate_submit", "Flag 已经提交过", 403);
            }

            if ($flag->team_id !== 0){
                // Flag 是限定队伍的
                if ($flag->team_id !== $team->team_id){
                    // 提交了其他队伍的 Flag
                    $team->banned = true;
                    $team->save();
                    return APIReturn::error("banned", "队伍已被封禁", 403);
                }
            }

            $ruleValidator = new RuleValidator($team->team_id, $level->rules);
            if (!$ruleValidator->check($team->logs) || Carbon::now('Asia/Shanghai') < $flag->challenge->release_time || Carbon::now('Asia/Shanghai') < $level->release_time){
                // 该队伍提交了还未开放的问题的 flag
                $team->banned = true;
                $team->save();
                return APIReturn::error("banned", "队伍已被封禁", 403);
            }

            // TODO: 题目完成时间与最小限制比对

            // 验证完毕 添加记录
            $successLog = new Log();
            $successLog->team_id = $team->team_id;
            $successLog->challenge_id = $flag->challenge_id;
            $successLog->level_id = $flag->challenge->level_id;
            $successLog->category_id = $level->category_id;
            $successLog->status = "correct";
            $successLog->flag = $request->input('flag');
            $successLog->score = 0.0;
            $successLog->save();
            // 动态分数应用
            $challengeLogs = Log::where([
                "challenge_id" => $flag->challenge_id,
                'status' => 'correct'
            ])->get();
            $dynamicScore = round($flag->challenge->score / (1 + $challengeLogs->count() / 10), 2);  // TODO: 临时公式
            Log::where("challenge_id", $flag->challenge_id)->update([
               "score" => $dynamicScore
            ]);

            return APIReturn::success([
                "score" => $dynamicScore
            ]);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}
