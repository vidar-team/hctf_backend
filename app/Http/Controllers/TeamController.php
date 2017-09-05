<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Team;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use APIReturn;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TeamController extends Controller
{
    private $team;
    public function __construct(Team $team) {
        $this->team = $team;
    }

    /**
     * 登陆
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Aklis
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $access_token = null;

        try {
            if (!$access_token = JWTAuth::attempt($credentials)) {
                return APIReturn::error("invalid_email_or_password", "Email 与密码不匹配", 401);
            }
        } catch (JWTAuthException $err) {
            return APIReturn::error("failed_to_create_token", "无法创建认证Token", 500);
        }

        return APIReturn::success(['access_token' => $access_token]);
    }

    /**
     * 注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Aklis
     */
    public function register(Request $request) {
        $input = $request->only('teamName', 'email', 'password');
        try {
            $team = $this->team->create([
                'teamName' => $input['teamName'],
                'email' => $input['email'],
                'password' => bcrypt($input['password']),
                'signUpTime' => Carbon::now('Asia/Shanghai'),
                'lastLoginTime' => Carbon::now('Asia/Shanghai'),
            ]);
        } catch (Exception $err) {
            return APIReturn::error("email_or_team_already_exist", "队伍或Email已经存在", 500);
        }

        return APIReturn::success([
            'msg' => 'Welcome to HCTF 2017!',
        ]);
    }

    public function getAuthInfo(Request $request) {
        $team = JWTAuth::parseToken()->toUser();
        $team->lastLoginTime = Carbon::now('Asia/Shanghai');
        $team->save();
        return APIReturn::success($team);
    }
//
//    public function refreshToken(Request $request) {
//        $team = JWTAuth::parseToken()->toUser();
//        $team->lastLoginTime = Carbon::now('Asia/Shanghai');
//        $team->save();
//        return APIReturn::success(['msg'=>'+1h']);
//    }

    public function listTeams(Request $request){
        $page = $request->input('page') ?? '1';
        try{
            $teams = DB::table('teams')->skip(($page - 1) * 20)->take(20)->get();
            return APIReturn::success($teams);
        }
        catch (\Exception $e){
            return APIReturn::error("database_error", "数据库读写错误", 500);
        }
    }
}