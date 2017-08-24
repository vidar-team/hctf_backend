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
                return APIReturn::error(401, ['invalid_email_or_password'], 401);
            }
        } catch (JWTAuthException $err) {
            return APIReturn::error(500, ['failed_to_create_token'], 500);
        }
        return response()->json(compact('access_token'));
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
            return APIReturn::error(500, ['msg' => 'Team/Email already exists.'], 500);
        }

        return APIReturn::success([
            'msg' => 'Welcome to HCTF 2017!',
        ]);
    }

    public function getAuthInfo(Request $request) {
        $team = JWTAuth::parseToken()->authenticate();
        return APIReturn::success(['team' => $team]);
    }
}
