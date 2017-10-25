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
use Illuminate\Support\Facades\Config;
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
            'flag' => 'array',
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
            $newChallenge->is_dynamic_flag = $request->input('isDynamicFlag');

            $newChallenge->save();
            $newChallenge->flags()->createMany($request->input('flag'));


            \Logger::info("一个新的 Challenge: " . $newChallenge->title . " 被创建");

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
            'challengeId' => 'required|integer'
        ], [
            'challengeId.required' => '缺少Challenge ID字段',
            'challengeId.integer' => 'Challenge ID不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }
        try {
            $challenge = Challenge::find($request->input('challengeId'));
            return APIReturn::success($challenge);
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
        $categories->each(function ($category) use ($validLevels, $team) {
            collect($category->levels)->each(function ($level) use ($validLevels, $team) {
                if ((new RuleValidator($team->team_id, $level->rules))->check($team->logs)) {
                    $validLevels->push($level->level_id);
                }
            });
        });

        $categories->each(function ($category) use ($validLevels, $result) {
            $result[$category->category_name] = $category->challenges->filter(function ($challenge) use ($validLevels) {
                return $validLevels->contains($challenge->level_id);
            })->groupBy('level_id');
        });

        return APIReturn::success($result);
    }

    /**
     * 重设基准分数
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function resetScore(Request $request)
    {
        $validator = Validator::make($request->only(['challengeId', 'score']), [
            'challengeId' => 'required|integer',
            'score' => 'required|numeric'
        ], [
            'challengeId.required' => '缺少 Challenge ID 字段',
            'challengeId.integer' => 'Challenge ID 字段不合法',
            'score.required' => '缺少基准分数字段',
            'score.numeric' => '基准分数字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        $score = $request->input('score');

        try {
            $count = Log::where('challenge_id', $request->input('challengeId'))->count();
            $dynamicScore = round($score / (1 + $count / 10), 2);  // TODO: 临时公式
            Log::where("challenge_id", $request->input('challengeId'))->update([
                "score" => $dynamicScore
            ]);
            return APIReturn::success();
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 获得 Flags 详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function getFlagsInfo(Request $request)
    {
        $validator = Validator::make($request->only(['challengeId']), [
            'challengeId' => 'required|integer'
        ], [
            'challengeId.required' => '缺少 Challenge ID 字段',
            'challengeId.integer' => 'Challenge ID 字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $challenge = Challenge::where('challenge_id', $request->input('challengeId'))->with('flags')->first();
            return APIReturn::success($challenge->flags);
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 修改 Challenge 基本信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function editChallenge(Request $request)
    {
        $validator = Validator::make($request->only(['challengeId', 'title', 'description', 'releaseTime']), [
            'challengeId' => 'required|integer',
            'title' => 'required',
            'description' => 'required',
            'releaseTime' => 'required|date'
        ], [
            'challengeId.required' => '缺少 Challenge ID 字段',
            'challengeId.integer' => 'Challenge ID 字段不合法',
            'title.required' => '缺少标题字段',
            'description' => '缺少描述字段',
            'releaseTime.required' => '缺少开放时间字段',
            'releaseTime.date' => '开放时间字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            $challenge = Challenge::find($request->input('challengeId'));
            if (!$challenge) {
                return APIReturn::error('challenge_not_found', 'challenge_not_found', 404);
            }

            $challenge->title = $request->input('title');
            $challenge->description = $request->input('description');
            $challenge->release_time = $request->input('releaseTime');
            $challenge->save();

            return APIReturn::success($challenge);
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 添加 Flag
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function addFlags(Request $request)
    {
        $validator = Validator::make($request->only(['challengeId', 'flag']), [
            'challengeId' => 'required|integer',
            'flag' => 'required|array'
        ], [
            'challengeId.required' => '缺少 Challenge ID 字段',
            'challengeId.integer' => 'Challenge ID 字段不合法',
            'flag.required' => '缺少 Flag 字段',
            'flag.array' => 'Flag 字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try{
            $challenge = Challenge::find($request->input('challengeId'));
            if (!$challenge){
                return APIReturn::error('challenge_not_found', '不存在的问题', 404);
            }
            $challenge->flags()->createMany($request->input('flag'));
            return APIReturn::success($challenge->flags);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 删除所有关联 Flag
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function deleteAllFlags(Request $request)
    {
        $validator = Validator::make($request->only(['challengeId']), [
            'challengeId' => 'required|integer'
        ], [
            'challengeId.required' => '缺少 Challenge ID 字段',
            'challengeId.integer' => 'Challenge ID 字段不合法'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            Flag::where('challenge_id', $request->input('challengeId'))->delete();
            return APIReturn::success();
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }

    /**
     * 删除 Challenge
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function deleteChallenge(Request $request)
    {
        $validator = Validator::make($request->only('challengeId'), [
            'challengeId' => 'required'
        ], [
            'challengeId.required' => '缺少 Challenge ID 字段'
        ]);

        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }

        try {
            // 删除关联的所有 Flag
            $challenge = Challenge::find($request->input('challengeId'));
            $challenge->flags()->delete();
            $challenge->logs()->delete();
            // 删除本体
            \Logger::info("Challenge " . $challenge->title . " 被删除");
            $challenge->delete();

            return APIReturn::success();
        } catch (\Exception $e) {
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
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
            'flag.required' => __('缺少 Flag 字段')
        ]);

        $team = JWTAuth::parseToken()->toUser();
        $team->load(['logs' => function ($query) {
            $query->where('status', 'correct');
        }]);


        if ($validator->fails()) {
            return APIReturn::error('invalid_parameters', $validator->errors()->all(), 400);
        }
        try {
            $flag = Flag::where('flag', $request->input('flag'))->first();
            $flagPrefix = \Config::get("ctf.flagPrefix");
            $flagSuffix = \Config::get("ctf.flagSuffix");

            if (!$flag) {
                //  Flag 不正确
                if (strlen($request->input('flag')) === 64 + strlen($flagPrefix) + strlen($flagSuffix)) {
                    // SHA256 长度为 64 位 / 可能是动态 Flag
                    $dynamicFlagChallenges = Challenge::with("flags")->where("is_dynamic_flag", "=", 1)->get();
                    foreach ($dynamicFlagChallenges as $c) {
                        if ($c->flags->count() > 0) {
                            if ($flagPrefix . hash("sha256", $team->token . $c->flags[0]->flag) . $flagSuffix === $request->input('flag')) {
                                $flag = $c->flags[0];
                            }
                        }
                    }
                }
                if (!$flag) {
                    \Logger::notice("队伍 " . $team->team_name . ' 提交 Flag: ' . $request->input('flag') . ' （错误）');
                    return APIReturn::error("wrong_flag", __("Flag 不正确"), 403);
                }
            }

            $level = Level::find($flag->challenge->level_id);

            if (Log::where([
                'challenge_id' => $flag->challenge_id,
                'status' => 'correct'
            ])->first()) {
                return APIReturn::error("duplicate_submit", __("Flag 已经提交过"), 403);
            }

            if ($flag->team_id !== 0) {
                // Flag 是限定队伍的
                if ($flag->team_id !== $team->team_id) {
                    // 提交了其他队伍的 Flag
                    $team->banned = true;
                    $team->save();
                    \Logger::info("队伍 " . $team->team_name . ' 由于提交其他队伍的 Flag 被系统自动封禁');
                    return APIReturn::error("banned", __("队伍已被封禁"), 403);
                }
            }

            $ruleValidator = new RuleValidator($team->team_id, $level->rules);
            if (!$ruleValidator->check($team->logs) || Carbon::now('Asia/Shanghai') < $flag->challenge->release_time || Carbon::now('Asia/Shanghai') < $level->release_time) {
                // 该队伍提交了还未开放的问题的 flag
                $team->banned = true;
                $team->save();
                \Logger::info("队伍 " . $team->team_name . ' 由于提交未开放任务的 Flag 被系统自动封禁');
                return APIReturn::error("banned", __("队伍已被封禁"), 403);
            }

            // 题目完成时间与最小限制比对
            if (json_decode($flag->challenge->config)->minimumSolveTime !== 0) {
                if ($ruleValidator->secondsAfterOpen() < json_decode($flag->challenge->config)->minimumSolveTime) {
                    // 做题时间太短
                    $team->banned = true;
                    $team->save();
                    \Logger::info("队伍 " . $team->team_name . ' 由于开放问题到提交正确 Flag 的时间间隔小于阈值被系统自动封禁');
                    return APIReturn::error("banned", __("队伍已被封禁"), 403);
                }
            }

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
            \Logger::info("队伍 " . $team->team_name . ' 提交问题 ' . $flag->challenge->title . ' Flag: ' . $request->input('flag') . ' （正确）');
            return APIReturn::success([
                "score" => $dynamicScore
            ]);
        } catch (\Exception $e) {
            return APIReturn::error("database_error", __("数据库读写错误"), 500);
        }
    }
}
