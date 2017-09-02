<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Team;
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
            return APIReturn::error("email_or_team_already_exist", ['msg' => '队伍或Email已经存在'], 500);
        }

        return APIReturn::success([
            'msg' => 'Welcome to HCTF 2017!',
        ]);
    }

    public function getAuthInfo(Request $request) {
        $team = JWTAuth::parseToken()->toUser();
        $team->lastLoginTime = Carbon::now('Asia/Shanghai');
        $team->save();
        return APIReturn::success(['team' => $team]);
    }
//
//    public function refreshToken(Request $request) {
//        $team = JWTAuth::parseToken()->toUser();
//        $team->lastLoginTime = Carbon::now('Asia/Shanghai');
//        $team->save();
//        return APIReturn::success(['msg'=>'+1h']);
//    }
}